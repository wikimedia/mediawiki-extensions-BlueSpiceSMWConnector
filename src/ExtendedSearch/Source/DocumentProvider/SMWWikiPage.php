<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\DocumentProvider;

use BS\ExtendedSearch\Source\DocumentProvider\WikiPage;

class SMWWikiPage extends WikiPage {
	protected $semanticData = [];

	/**
	 *
	 * @param string $uri
	 * @param \WikiPage $wikipage
	 * @return array
	 */
	public function getDataConfig( $uri, $wikipage ) {
		$aDC = $this->oDecoratedDP->getDataConfig( $uri, $wikipage );

		$aDC = array_merge( $aDC, parent::getDataConfig( $uri, $wikipage ) );
		$this->getSemanticData( $wikipage );

		$aDC = array_merge( [
			'smwproperty' => $this->semanticData,
			// This field is necessary for filtering
			'smwproperty_agg' => $this->getSemanticValueArray()
		], $aDC );

		return $aDC;
	}

	public function __destruct() {
		parent::__destruct();
		$this->semanticData = null;
		unset( $this->semanticData );
	}

	/**
	 * Gets all semantic properties for a given page
	 * and sets correctly formatted array of values to be indexed as class variable
	 *
	 * @param \WikiPage $wikipage
	 */
	protected function getSemanticData( $wikipage ) {
		$subject = \SMW\DIWikiPage::newFromTitle( $wikipage->getTitle() );
		$store = \SMW\StoreFactory::getStore();

		$properties = $store->getProperties( $subject );

		$smwData = [];
		foreach ( $properties as $property ) {
			if ( $property->isUserDefined() == false ) {
				// If only user properties are wanted
				// continue;
			}
			$name = $property->getKey();
			$values = $store->getPropertyValues( $subject, $property );
			$label = $property->getLabel() ?: $name;

			$type = 'string';
			$parsedValues = [];
			foreach ( $values as $value ) {
				$parsedValues[] = $this->parseSemanticValue( $value, $type );
			}

			if ( count( $parsedValues ) == 1 ) {
				// No need to store array for single value
				$parsedValues = $parsedValues[0];
			}

			$smwData[] = [
				"name" => $label,
				"value" => $parsedValues,
				"type" => $type
			];
		}
		$this->semanticData = $smwData;
	}

	/**
	 *
	 * @param \SMWDataItem $value
	 * @param string &$type
	 * @return string
	 */
	protected function parseSemanticValue( $value, &$type ) {
		if ( $value instanceof \SMW\DIWikiPage && $value->getTitle() instanceof \Title ) {
			$type = 'title';
			return $value->getTitle()->getPrefixedText();
		} elseif ( $value instanceof \SMWDINumber ) {
			$type = 'numeric';
			return $value->getNumber();
		} elseif ( $value instanceof \SMWDIBoolean ) {
			$type = 'boolean';
			return $value->getBoolean() ? 1 : 0;
		} elseif ( $value instanceof \SMWDITime ) {
			$type = 'datetime';
			return $value->getMwTimestamp( TS_ISO_8601 );
		} else {
			// Use default serialization
			return $value->__toString();
		}
	}

	protected function getSemanticValueArray() {
		$valueArray = [];
		foreach ( $this->semanticData as $smwDataItem ) {
			$value = $smwDataItem['value'];
			if ( !is_array( $value ) ) {
				$value = [ $value ];
			}

			foreach ( $value as $singleValue ) {
				$valueArray[] = $smwDataItem['name'] . '|' . $singleValue;
			}
		}
		return $valueArray;
	}
}
