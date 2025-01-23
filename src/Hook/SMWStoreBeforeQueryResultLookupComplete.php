<?php

namespace BlueSpice\SMWConnector\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;

abstract class SMWStoreBeforeQueryResultLookupComplete extends Hook {

	/**
	 *
	 * @var \SMW\Store
	 */
	protected $store = null;

	/**
	 *
	 * @var \SMWQuery
	 */
	protected $query = null;

	/**
	 * ATTENTION: THE PARAMETER IS NULL DURING THE HOOK CALL, BUT IF RETURNING
	 * FALSE THIS MUST BE SET TO \SMWQueryResult
	 * @var \SMWQueryResult|null
	 */
	protected $result = null;

	/**
	 *
	 * @var \SMW\QueryEngine
	 */
	protected $slaveQueryEngine = null;

	/**
	 *
	 * @param \IContextSource $context
	 * @param Config $config
	 * @param \SMW\Store $store
	 * @param \SMWQuery $query
	 * @param null &$result
	 * @param \SMW\QueryEngine $slaveQueryEngine
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
	 * @param \SMW\Store $store
	 * @param \SMWQuery $query
	 * @param \SMWQueryResult &$result
	 * @param \SMW\SQLStore\QueryEngine\QueryEngine $slaveQueryEngine
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
