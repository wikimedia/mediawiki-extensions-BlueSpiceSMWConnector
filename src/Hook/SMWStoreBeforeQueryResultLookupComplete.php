<?php

namespace BlueSpice\SMWConnector\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use SMW\Query\QueryResult;
use SMW\QueryEngine;
use SMW\SQLStore\QueryEngine\QueryEngine as SQLStoreQueryEngine;
use SMW\Store;
use SMWQuery;

abstract class SMWStoreBeforeQueryResultLookupComplete extends Hook {

	/**
	 *
	 * @var Store
	 */
	protected $store = null;

	/**
	 *
	 * @var SMWQuery
	 */
	protected $query = null;

	/**
	 * ATTENTION: THE PARAMETER IS NULL DURING THE HOOK CALL, BUT IF RETURNING
	 * FALSE THIS MUST BE SET TO QueryResult
	 * @var QueryResult|null
	 */
	protected $result = null;

	/**
	 *
	 * @var QueryEngine
	 */
	protected $slaveQueryEngine = null;

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Store $store
	 * @param SMWQuery $query
	 * @param null &$result
	 * @param QueryEngine $slaveQueryEngine
	 */
	public function __construct( $context, $config, $store, $query, &$result, $slaveQueryEngine ) {
		parent::__construct( $context, $config );

		$this->store = $store;
		$this->query = $query;
		$this->result =& $result;
		$this->slaveQueryEngine = $slaveQueryEngine;
	}

	/**
	 *
	 * @param Store $store
	 * @param SMWQuery $query
	 * @param QueryResult &$result
	 * @param SQLStoreQueryEngine $slaveQueryEngine
	 * @return bool
	 */
	public static function callback( $store, $query, &$result, $slaveQueryEngine ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$store,
			$query,
			$result,
			$slaveQueryEngine
		);
		return $hookHandler->process();
	}
}
