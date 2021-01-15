<?php

namespace BlueSpice\SMWConnector\Data\Ask;

use FormatJson;

class ReaderParams extends \BlueSpice\Data\ReaderParams {
	const PARAM_PROPS = 'props';
	/** @var array  */
	protected $props = [];

	/**
	 * @param array $params
	 */
	public function __construct( $params = [] ) {
		parent::__construct( $params );
		$this->setIfAvailable( $this->props, $params, static::PARAM_PROPS );
		if ( is_string( $this->props ) ) {
			$this->props = FormatJson::decode( $this->props, true );
		}
	}

	/**
	 * @return array
	 */
	public function getProps() {
		return $this->props;
	}
}
