<?php

namespace BlueSpice\SMWConnector\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use SMW\Query\QueryResult;
use SMW\Store;

abstract class SMWStoreAfterQueryResultLookupComplete extends Hook {

	/**
	 *
	 * @var Store
	 */
	protected $store = null;

	/**
	 * @var QueryResult
	 */
	protected $result = null;

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Store $store
	 * @param QueryResult &$result
	 */
	public function __construct( $context, $config, $store, &$result ) {
		parent::__construct( $context, $config );

		$this->store = $store;
		$this->result =& $result;
	}

	/**
	 *
	 * @param Store $store
	 * @param QueryResult &$result
	 * @return bool|null
	 */
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
