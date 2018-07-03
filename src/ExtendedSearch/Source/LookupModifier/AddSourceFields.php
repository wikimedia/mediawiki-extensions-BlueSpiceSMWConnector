<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\LookupModifier;

use BS\ExtendedSearch\Source\LookupModifier\Base;

class AddSourceFields extends Base {

	public function apply() {
		$simpleQS = $this->oLookup->getQueryString();
		//Search in basename field and boost it by 3
		$fields = ['smwproperty'];
		if( isset( $simpleQS['fields'] ) && is_array( $simpleQS['fields'] ) ) {
			$simpleQS['fields'] = array_merge( $simpleQS['fields'], $fields );
		} else {
			$simpleQS['fields'] = $fields;
		}

		$this->oLookup->setQueryString( $simpleQS );
	}

	public function undo() {
		$simpleQS = $this->oLookup->getQueryString();

		if( isset( $simpleQS['fields'] ) && is_array( $simpleQS['fields'] ) ) {
			$simpleQS['fields'] = array_diff( $simpleQS['fields'], ['smwproperty'] );
		}

		$this->oLookup->setQueryString( $simpleQS );
	}
}
