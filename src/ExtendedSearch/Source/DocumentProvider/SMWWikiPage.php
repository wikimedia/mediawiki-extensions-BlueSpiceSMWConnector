<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\DocumentProvider;

use BS\ExtendedSearch\Source\DocumentProvider\WikiPage;

class SMWWikiPage extends WikiPage {
	/**
	 *
	 * @param string $uri
	 * @param \WikiPage $wikipage
	 * @return array
	 */
	public function getDataConfig( $uri, $wikipage ) {
		$aDC = $this->oDecoratedDP->getDataConfig( $uri, $wikipage );

		$aDC = array_merge( $aDC, parent::getDataConfig( $uri, $wikipage ) );

		$aDC = array_merge( [
			'smwproperty' => $this->getSemanticData( $wikipage ),
			//This field is necessary for filtering
			'smwproperty_agg' => $this->getSemanticValueArray( $wikipage )
		], $aDC);

		return $aDC;
	}

	/**
	 * Gets all semantic properties for a given page
	 * and returns correctly formatted array of values to be indexed
	 *
	 * @param \WikiPage $wikipage
	 * @return array
	 */
	protected function getSemanticData( $wikipage ) {
		$subject = \SMW\DIWikiPage::newFromTitle( $wikipage->getTitle() );
		$store = \SMW\StoreFactory::getStore();

		$properties = $store->getProperties( $subject );

		$smwData = [];
		foreach( $properties as $property ) {
			if( $property->isUserDefined() == false ) {
				//If only user properties are wanted
				//continue;
			}
			$name = $property->getKey();
			$values = $store->getPropertyValues( $subject, $property );
			$label = $property->getLabel() ?: $name;

			$type = 'string';
			$parsedValues = [];
			foreach( $values as $value ) {
				$parsedValues[] = $this->parseSemanticValue( $value, $type );
			}

			if( count( $parsedValues ) == 1 ) {
				//No need to store array for single value
				$parsedValues = $parsedValues[0];
			}

			$smwData[] = [
				"name" => $label,
				"value" => $parsedValues,
				"type" => $type
			];
		}
		return $smwData;
	}

	/**
	 *
	 * @param \SMWDataItem $value
	 * @return string
	 */
	protected function parseSemanticValue( $value, &$type ) {
		if( $value instanceof \SMW\DIWikiPage && $value->getTitle() instanceof \Title ) {
			$type = 'title';
			return $value->getTitle()->getPrefixedText();
		} else if( $value instanceof \SMWDINumber ) {
			$type = 'numeric';
			return $value->getNumber();
		} else if( $value instanceof \SMWDIBoolean ) {
			$type = 'boolean';
			return $value->getBoolean() ? 1 : 0;
		} else if( $value instanceof \SMWDITime ) {
			$type = 'datetime';
			return $value->getMwTimestamp( TS_ISO_8601 );
		} else {
			//Use default serialization
			return $value->__toString();
		}
	}

	protected function getSemanticValueArray( $wikipage ) {
		$smwData = $this->getSemanticData( $wikipage );
		$valueArray = [];
		foreach( $smwData as $smwDataItem ) {
			$value = $smwDataItem['value'];
			if( !is_array( $value ) ) {
				$value = [$value];
			}

			foreach( $value as $singleValue ) {
				$valueArray[] = $smwDataItem['name'] . '|' . $singleValue;
			}
		}
		return $valueArray;
	}
}
