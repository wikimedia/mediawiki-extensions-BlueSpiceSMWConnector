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
		// The "page" column is the subject itself, not a property
		$prefix = $filter->getField() === 'page' ? '' : $filter->getField() . '::';
		switch ( $operator ) {
			case 'sw':
				return '[[' . $prefix . '~' . $filter->getValue() . '*]]';
			case 'ew':
				return '[[' . $prefix . '~*' . $filter->getValue() . ']]';
			case 'nct':
				return '[[' . $prefix . '!~*' . $filter->getValue() . '*]]';
			case 'ct':
			default:
				return ( new AnyDescription( $filter->getField(), $filter->getValue() ) )
					->getQueryString( true );
		}
	}
}
