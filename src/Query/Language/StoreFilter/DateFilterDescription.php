<?php

namespace BlueSpice\SMWConnector\Query\Language\StoreFilter;

class DateFilterDescription extends FilterDescription {

	/**
	 * @inheritDoc
	 */
	public function getQueryString( $asValue = false ) {
		$unix = strtotime( $this->filter->getValue() );
		if ( $unix === false ) {
			return '';
		}

		$start = date( 'Ymd000000', $unix );
		$end = date( 'Ymd235959', $unix );

		$string = '';
		$operator = $this->filter->getComparison();
		if ( $operator === 'eq' ) {
			$string = '[[' . $this->filter->getField() . '::>' . $start . ']]' .
				'[[' . $this->filter->getField() . '::<' . $end . ']]';
		}
		if ( $operator === 'lt' ) {
			$string = '[[' . $this->filter->getField() . '::<<' . $start . ']]';
		}
		if ( $operator === 'gt' ) {
			$string = '[[' . $this->filter->getField() . '::>>' . $end . ']]';
		}

		if ( $string && $asValue ) {
			return '<q>' . $string . '</q>';
		}

		return $string;
	}
}
