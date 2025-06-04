<?php

namespace BlueSpice\SMWConnector\SmartListMode;

use BlueSpice\SmartList\Mode\BaseMode;
use MediaWiki\Context\RequestContext;
use MWStake\MediaWiki\Component\InputProcessor\Processor\IntValue;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\Query\QueryResult;
use SMW\Services\ServicesFactory;
use SMW\Store;
use SMWDataItem;
use SMWDIBoolean;
use SMWDITime;
use SMWQuery;
use SMWQueryProcessor;

class DataQueryMode extends BaseMode {

	public const ATTR_CATEGORIES = 'categories';
	public const ATTR_NAMESPACES = 'namespaces';
	public const ATTR_MODIFIED = 'modified';
	public const ATTR_PRINTOUTS = 'printouts';
	public const ATTR_FORMAT = 'format';

	/** @var string */
	private string $listType = 'ul';

	/**
	 * @return string
	 */
	public function getKey(): string {
		return 'dataquery';
	}

	/**
	 * @return array
	 */
	public function getParams(): array {
		$params = parent::getParams();
		$params[static::ATTR_COUNT] = ( new IntValue() )->setDefaultValue( 10 );
		$params[static::ATTR_CATEGORIES] = [
			'type' => 'string'
		];
		$params[static::ATTR_NAMESPACES] = [
			'type' => 'string'
		];
		$params[static::ATTR_MODIFIED] = [
			'type' => 'string',
			'required' => false,
			'default' => ''
		];
		$params[static::ATTR_PRINTOUTS] = [
			'type' => 'string',
			'required' => false,
			'default' => ''
		];
		$params[static::ATTR_FORMAT] = [
			'type' => 'string',
			'required' => false,
			'default' => 'ul'
		];
		return $params;
	}

	/**
	 * @param array $args
	 * @param RequestContext $context
	 * @return array
	 */
	public function getList( $args, $context ): array {
		$this->listType = $args[self::ATTR_FORMAT];

		$categories = $this->createSMWformat( $args[self::ATTR_CATEGORIES], 'categories' );
		$namespaces = $this->createSMWformat( $args[self::ATTR_NAMESPACES], 'namespaces' );
		$modified = $this->createSMWformat( $args[self::ATTR_MODIFIED], 'modified' );
		$printouts = $this->createSMWformat( $args[self::ATTR_PRINTOUTS], 'printouts' );

		$printoutsArray = explode( '|', $args[self::ATTR_PRINTOUTS] );
		$printoutsArray = array_map( 'strtolower', $printoutsArray );

		$query = '{{#ask:' . $categories . $namespaces . $modified . $printouts . '}}';
		$queryResult = $this->runSMWQuery( $query, $args['count'] );
		$results = $queryResult->getResults();

		$store = ServicesFactory::getInstance()->getStore();
		$list = [];
		foreach ( $results as $DIWikiPage ) {
			$title = $DIWikiPage->getTitle();
			if ( $title === null || !$title->exists() ) {
				continue;
			}

			$smwData = '';
			if ( $printouts ) {
				$smwData .= $this->getProperties( $DIWikiPage, $printoutsArray, $store );
			}

			$list[] = [
				'PREFIXEDTITLE' => $title->getPrefixedText(),
				'META' => $smwData
			];
		}

		return $list;
	}

	/**
	 * @param string $args
	 * @param string $format
	 * @return string
	 */
	public function createSMWformat( string $args, string $format ): string {
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
					$argsString .= "[[Category:$arg]]";
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
					$argsString .= "[[$arg:+]]";
					$multiple = true;
				}
				break;
			case 'modified':
				foreach ( $argsArray as $arg ) {
					$argsString .= "[[Modification date::$arg]]";
				}
				break;
			case 'printouts':
				foreach ( $argsArray as $arg ) {
					$argsString .= "|?$arg";
				}
				break;
		}

		return $argsString;
	}

	/**
	 * @param string $query
	 * @param int $count
	 * @return QueryResult
	 */
	private function runSMWQuery( string $query, int $count ): QueryResult {
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
	 * @param DIWikiPage $DIWikiPage
	 * @param array $printouts
	 * @param Store $store
	 * @return string
	 */
	public function getProperties( DIWikiPage $DIWikiPage, array $printouts, Store $store ): string {
		$propertiesData = [];
		$semanticData = $store->getSemanticData( $DIWikiPage );
		$DIProperties = $semanticData->getProperties();
		/** @var DIProperty $standardProperty */
		foreach ( $DIProperties as $DIProperty ) {
			$propertiesData[] = $this->getPropertyData( $DIWikiPage, $DIProperty, $printouts, $store );
		}

		$first = true;
		$data = '';
		foreach ( $propertiesData as $propertyData ) {
			if ( $propertyData === '' ) {
				continue;
			}

			if ( $first ) {
				$data .= '(';
			} else {
				$data .= ', ';
			}
			$data .= $propertyData;
			$first = false;
		}
		$data .= ')';

		return $data;
	}

	/**
	 * @param DIWikiPage $DIWikiPage
	 * @param DIProperty $DIProperty
	 * @param array $printouts
	 * @param Store $store
	 * @return string
	 */
	public function getPropertyData(
		DIWikiPage $DIWikiPage, DIProperty $DIProperty, array $printouts, Store $store
	): string {
		$property = $DIProperty->getCanonicalLabel();
		if ( !in_array( strtolower( $property ), $printouts ) ) {
			return '';
		}

		$DIValues = $store->getPropertyValues( $DIWikiPage, $DIProperty );
		$values = [];
		foreach ( $DIValues as $DIValue ) {
			if ( $DIValue instanceof SMWDITime ) {
				$timestamp = $DIValue->getMwTimestamp();
				$values[] = date( 'd F Y H:i:s', $timestamp );
			} elseif ( $DIValue instanceof SMWDIBoolean ) {
				$values[] = $DIValue->getBoolean() ? 'true' : 'false';
			} elseif ( $DIValue instanceof DIWikiPage ) {
				$values[] = "[[{$DIValue->getTitle()->getPrefixedText()}]]";
			} else {
				$value = $DIValue->getSerialization();
				$hashPosition = strpos( $value, '#' );
				if ( $hashPosition ) {
					$value = substr( $value, 0, $hashPosition );
				}
				if ( $DIValue->getDIType() === SMWDataItem::TYPE_WIKIPAGE ) {
					$values[] = "[[$value]]";
				} else {
					$values[] = $value;
				}
			}
		}
		$values = implode( ',', $values );

		return "[[Property:$property|''$property'']]: $values";
	}

	/**
	 * @return string
	 */
	public function getListType(): string {
		return $this->listType;
	}
}
