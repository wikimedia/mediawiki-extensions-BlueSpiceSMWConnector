<?php

namespace BlueSpice\SMWConnector\Query\Language\StoreFilter;

use BlueSpice\Data\Filter;
use SMW\Query\Language\Description;

class FilterDescription extends Description {
	/** @var string[] */
	protected $operatorMap = [
		'eq' => '',
		'lt' => '<<',
		'gt' => '>>',
		'neq' => '!',
	];

	/** @var Filter */
	protected $filter;

	/**
	 *
	 * @param Filter $filter
	 */
	public function __construct( Filter $filter ) {
		$this->filter = $filter;
	}

	/**
	 * @inheritDoc
	 */
	public function getFingerprint() {
		return 'DT:' . md5(
			$this->filter->getField() . '#' .
			$this->filter->getValue() . '#' .
			$this->filter->getComparison()
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getQueryString( $asValue = false ) {
		$operator = $this->filter->getComparison();
		if ( !isset( $this->operatorMap[$operator] ) ) {
			return '';
		}
		$operator = $this->operatorMap[$operator];

		return '[[' . $this->filter->getField() . '::' .
			$operator . $this->filter->getValue() . ']]';
	}

	/**
	 * @inheritDoc
	 */
	public function isSingleton() {
		return true;
	}
}
