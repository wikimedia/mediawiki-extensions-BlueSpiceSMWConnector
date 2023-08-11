<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\LookupModifier;

use BS\ExtendedSearch\Source\LookupModifier\LookupModifier;

/**
 * Class ParseSMWFilters
 * This class gets encoded filters for SMW properties and
 * converts them to filters usable by Elastic
 *
 */
class ParseSMWFilters extends LookupModifier {
	/** @var array */
	protected $originalFilters = [ 'terms' => [], 'term' => [] ];
	/** @var array */
	protected $parsedFilters = [ 'terms' => [], 'term' => [] ];

	public function apply() {
		$filters = $this->lookup->getFilters();
		$terms = $filters['terms'] ?? [];
		foreach ( $terms as $key => $value ) {
			if ( $this->getSMWKey( $key ) ) {
				$this->addTermsFilter( $key, $value );
			}
		}
	}

	/**
	 *
	 * @param string $key
	 * @param array|string $value
	 */
	protected function addTermsFilter( $key, $value ) {
		if ( is_array( $value ) === false ) {
			$value = [ $value ];
		}

		// remove the original
		$this->lookup->removeTermsFilter( $key, $value );
		$this->originalFilters['terms'][$key] = $value;

		$parsedKey = $this->getSMWKey( $key );
		$builtValues = $this->buildValues( $parsedKey, $value );
		$this->lookup['query']['bool']['filter'][] = [
			"terms" => [
				"smwproperty_agg" => $builtValues
			]
		];
		$this->parsedFilters['terms'][] = $builtValues;
	}

	/**
	 * Parses base64 encoded string, and returns name of
	 * SMW property, if passed key is a property
	 *
	 * @param string $key
	 * @return bool|string
	 */
	protected function getSMWKey( $key ) {
		$decodedKey = base64_decode( $key );
		if ( strpos( $decodedKey, 'smwproperty:' ) !== 0 ) {
			// Not an SMW key
			return false;
		}
		return ltrim( $decodedKey, 'smwproperty:' );
	}

	/**
	 *
	 * @param string $key
	 * @param string[] $value
	 * @return array
	 */
	protected function buildValues( $key, $value ) {
		$res = [];
		foreach ( $value as $singleValue ) {
			$res[] = $key . '|' . $singleValue;
		}
		return $res;
	}

	public function undo() {
		// Restore original filters
		foreach ( $this->originalFilters['terms'] as $key => $value ) {
			$this->lookup->addTermsFilter( $key, $value );
		}
		// Remove filters we added here
		foreach ( $this->parsedFilters['terms'] as $value ) {
			$this->lookup->removeTermsFilter( 'smwproperty_agg', $value );
		}
	}
}
