<?php

namespace BlueSpice\SMWConnector\SmartListMode;

use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\SmartList\Mode\BaseMode;
use MediaWiki\Permissions\PermissionManager;
use MessageLocalizer;
use RequestContext;
use SMW\Query\QueryResult;
use SMW\Services\ServicesFactory;
use SMWQuery;
use SMWQueryProcessor;
use TitleFactory;
use Wikimedia\Rdbms\ILoadBalancer;

class SMWReportMode extends BaseMode {

	public const ATTR_CATEGORIES = 'categories';
	public const ATTR_NAMESPACES = 'namespaces';
	public const ATTR_MODIFIED = 'modified';
	public const ATTR_PRINTOUTS = 'printouts';

	/** @var PermissionManager */
	private $permissionManager;

	/** @var ILoadBalancer */
	private $lb;

	/** @var TitleFactory */
	private $titleFactory;

	/** @var MessageLocalizer */
	private $messageLocalizer;

	/**
	 *
	 * @param PermissionManager $permissionManager
	 * @param ILoadBalancer $lb
	 * @param TitleFactory $titleFactory
	 */
	public function __construct(
		PermissionManager $permissionManager,
		ILoadBalancer $lb,
		TitleFactory $titleFactory
	) {
		$this->permissionManager = $permissionManager;
		$this->lb = $lb;
		$this->titleFactory = $titleFactory;
		$this->messageLocalizer = RequestContext::getMain();
	}

	/**
	 * @return string
	 */
	public function getKey(): string {
		return 'whatlinkshere';
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
		$args['categories'] = $args[self::ATTR_CATEGORIES];
		$args['namespaces'] = $args[self::ATTR_NAMESPACES];
		$args['modified'] = $args[self::ATTR_MODIFIED];
		$args['printouts'] = $args[self::ATTR_PRINTOUTS];

		$categories = $this->createSMWformat( $args['categories'], 'categories' );
		$namespaces = $this->createSMWformat( $args['namespaces'], 'namespaces' );
		$modified = $this->createSMWformat( $args['modified'], 'modified' );
		$printouts = $this->createSMWformat( $args['printouts'], 'printouts' );

		$query = '{{#ask:' . $categories . $namespaces . $modified . $printouts . '}}';
		$queryResult = $this->runSMWQuery( $query, $args['count'] );
		$results = $queryResult->getResults();

		$list = [];
		foreach ( $results as $wikiPage ) {
			$title = $this->titleFactory->newFromDBkey( $wikiPage->getDBkey() );
			$data = [
				'PREFIXEDTITLE' => $title->getPrefixedText()
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
	private function createSMWformat( $args, $format ): string {
		if ( empty( $args ) ) {
			return '';
		}

		$argsArray = explode( '|', $args );
		$argsString = '';
		switch ( $format ) {
			case 'categories':
				foreach ( $argsArray as $arg ) {
					$argsString .= '[[Category:' . $arg . ']]';
				}
				break;
			case 'namespaces':
				foreach ( $argsArray as $arg ) {
					$argsString .= '[[' . $arg . ':+]]';
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
