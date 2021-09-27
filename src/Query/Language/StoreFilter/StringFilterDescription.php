<?php

namespace BlueSpice\SMWConnector\Query\Language\StoreFilter;

use BlueSpice\SMWConnector\Query\Language\AnyDescription;

class StringFilterDescription extends FilterDescription {

	/**
	 * @inheritDoc
	 */
	public function getQueryString( $asValue = false ) {
		$filter = $this->filter;
		$operator = $filter->getComparison();
		switch ( $operator ) {
			case 'sw':
				return '[[' . $filter->getField() . '::~' . $filter->getValue() . '*]]';
			case 'ew':
				return '[[' . $filter->getField() . '::~*' . $filter->getValue() . ']]';
			case 'nct':
				return '[[' . $filter->getField() . '::!~*' . $filter->getValue() . '*]]';
			case 'ct':
			default:
				return ( new AnyDescription( $filter->getField(), $filter->getValue() ) )
					->getQueryString( true );
		}
	}
}
