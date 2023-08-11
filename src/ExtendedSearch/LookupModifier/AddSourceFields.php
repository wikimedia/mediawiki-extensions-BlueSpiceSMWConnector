<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\LookupModifier;

use BS\ExtendedSearch\Source\LookupModifier\LookupModifier;

class AddSourceFields extends LookupModifier {

	public function apply() {
		$simpleQS = $this->lookup->getQueryString();
		// Search in basename field and boost it by 3
		$fields = [ 'smwproperty' ];
		if ( isset( $simpleQS['fields'] ) && is_array( $simpleQS['fields'] ) ) {
			$simpleQS['fields'] = array_merge( $simpleQS['fields'], $fields );
		} else {
			$simpleQS['fields'] = $fields;
		}

		$this->lookup->setQueryString( $simpleQS );
	}

	public function undo() {
		$simpleQS = $this->lookup->getQueryString();

		if ( isset( $simpleQS['fields'] ) && is_array( $simpleQS['fields'] ) ) {
			$simpleQS['fields'] = array_diff( $simpleQS['fields'], [ 'smwproperty' ] );
		}

		$this->lookup->setQueryString( $simpleQS );
	}
}
