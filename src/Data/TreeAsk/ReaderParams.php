<?php

namespace BlueSpice\SMWConnector\Data\TreeAsk;

use BlueSpice\SMWConnector\Data\Ask\ReaderParams as Base;

class ReaderParams extends Base {
	public const PARAM_NODE = 'node';

	/** @var string */
	protected $node = '';

	/**
	 * @param array $params
	 */
	public function __construct( $params = [] ) {
		parent::__construct( $params );
		$this->setIfAvailable( $this->node, $params, static::PARAM_NODE );
	}

	/**
	 * @return string
	 */
	public function getNode() {
		return $this->node;
	}
}
