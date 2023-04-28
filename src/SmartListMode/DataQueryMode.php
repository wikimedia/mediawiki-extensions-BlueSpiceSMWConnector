<?php

namespace BlueSpice\SMWConnector\SmartListMode;

use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\SmartList\Mode\BaseMode;
use RequestContext;
use SMW\Query\QueryResult;
use SMW\Services\ServicesFactory;
use SMWQuery;
use SMWQueryProcessor;

class DataQueryMode extends BaseMode {

	public const ATTR_CATEGORIES = 'categories';
	public const ATTR_NAMESPACES = 'namespaces';
	public const ATTR_MODIFIED = 'modified';
	public const ATTR_PRINTOUTS = 'printouts';

	/**
	 * @return string
	 */
	public function getKey(): string {
		return 'dataquery';
	}

	/**
	 * @return IParamDefinition[]
	 */
	public function getParams(): array {
		$parentParams = parent::getParams();
		return array_merge( $parentParams, [
			new ParamDefinition(
				ParamType::INTEGER,
				static::ATTR_COUNT,
				10
			),
			new ParamDefinition(
				ParamType::STRING,
				static::ATTR_CATEGORIES,
				''
			),
			new ParamDefinition(
				ParamType::STRING,
				static::ATTR_NAMESPACES,
				''
			),
			new ParamDefinition(
				ParamType::STRING,
				static::ATTR_MODIFIED,
				''
			),
			new ParamDefinition(
				ParamType::STRING,
				static::ATTR_PRINTOUTS,
				''
			)
		] );
	}

	/**
	 * @param array $args
	 * @param RequestContext $context
	 * @return array
	 */
	public function getList( $args, $context ): array {
		$categories = $this->createSMWformat( $args[self::ATTR_CATEGORIES], 'categories' );
		$namespaces = $this->createSMWformat( $args[self::ATTR_NAMESPACES], 'namespaces' );
		$modified = $this->createSMWformat( $args[self::ATTR_MODIFIED], 'modified' );
		$printouts = $this->createSMWformat( $args[self::ATTR_PRINTOUTS], 'printouts' );

		$query = '{{#ask:' . $categories . $namespaces . $modified . $printouts . '}}';
		$queryResult = $this->runSMWQuery( $query, $args['count'] );
		$results = $queryResult->getResults();

		$printoutsArray = explode( '|', $args[self::ATTR_PRINTOUTS] );
		$printoutsArray = array_map( 'strtolower', $printoutsArray );
		$store = ServicesFactory::getInstance()->getStore();
		$list = [];
		foreach ( $results as $DIWikiPage ) {
			$title = $DIWikiPage->getTitle();
			$DIProperties = $store->getProperties( $DIWikiPage );
			$multiple = false;
			$smwData = '';
			/** @var \SMW\DIProperty $DIProperty */
			foreach ( $DIProperties as $DIProperty ) {
				$property = $DIProperty->getKey();
				if ( in_array( strtolower( $property ), $printoutsArray ) ) {
					$DIValues = $store->getPropertyValues( $DIWikiPage, $DIProperty );
					$values = [];
					/** @var \SMW\DIWikiPage $DIValue */
					foreach ( $DIValues as $DIValue ) {
						$values[] = $DIValue->getDBkey();
					}
					$values = implode( ',', $values );
					if ( $multiple ) {
						$smwData .= ', ';
					} else {
						$smwData .= '(';
					}
					$smwData .= $property . ':' . $values;
					$multiple = true;
				}
			}
			if ( $smwData ) {
				$smwData .= ')';
			}

			$data = [
				'PREFIXEDTITLE' => $title->getPrefixedText(),
				'META' => $smwData
			];
			$list[] = $data;
		}

		return $list;
	}

	/**
	 * @param string $args
	 * @param string $format
	 * @return string
	 */
	public function createSMWformat( $args, $format ): string {
		if ( empty( $args ) ) {
			return '';
		}

		$argsArray = explode( '|', $args );
		$argsString = '';
		$multiple = false;
		switch ( $format ) {
			case 'categories':
				foreach ( $argsArray as $arg ) {
					if ( $multiple ) {
						$argsString .= 'OR';
					}
					$argsString .= '[[Category:' . $arg . ']]';
					$multiple = true;
				}
				break;
			case 'namespaces':
				foreach ( $argsArray as $arg ) {
					if ( $multiple ) {
						$argsString .= 'OR';
					}
					if ( strtolower( $arg ) === 'main' ) {
						$arg = '';
					}
					$argsString .= '[[' . $arg . ':+]]';
					$multiple = true;
				}
				break;
			case 'modified':
				foreach ( $argsArray as $arg ) {
					$argsString .= '[[Modification date::' . $arg . ']]';
				}
				break;
			case 'printouts':
				foreach ( $argsArray as $arg ) {
					$argsString .= '|?' . $arg;
				}
				break;
		}

		return $argsString;
	}

	/**
	 * @param array $query
	 * @param int $count
	 * @return QueryResult
	 */
	private function runSMWQuery( $query, $count ): QueryResult {
			[ $qs, $parameters, $printouts ] =
				SMWQueryProcessor::getComponentsFromFunctionParams(
					[ $query ], false
				);
			$parameters['limit'] = $count;

			$query = SMWQueryProcessor::createQuery(
				$qs,
				SMWQueryProcessor::getProcessedParams( $parameters, $printouts ),
				SMWQueryProcessor::SPECIAL_PAGE,
				'',
				$printouts
			);
			$query->setOption( SMWQuery::PROC_CONTEXT, 'API' );

			return ServicesFactory::getInstance()->getStore()->getQueryResult( $query );
	}

	/**
	 * @return string
	 */
	public function getListType(): string {
		return 'ul';
	}
}
