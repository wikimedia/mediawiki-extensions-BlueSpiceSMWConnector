<?php

namespace BlueSpice\SMWConnector\Hook;

use BlueSpice\Hook;

abstract class SMWStoreAfterQueryResultLookupComplete extends Hook {

	/**
	 *
	 * @var \SMW\Store
	 */
	protected $store = null;

	/**
	 * @var \SMWQueryResult
	 */
	protected $result = null;

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \SMW\Store $store
	 * @param \SMWQueryResult $result
	 */
	public function __construct( $context, $config, $store, &$result ) {
		parent::__construct( $context, $config );

		$this->store = $store;
		$this->result =& $result;
	}

	public static function callback( $store, &$result ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$store,
			$result
		);
		return $hookHandler->process();
	}
}