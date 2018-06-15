<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\DocumentProvider;

use BS\ExtendedSearch\Source\DocumentProvider\WikiPage;

class SMWWikiPage extends WikiPage {
	/**
	 *
	 * @param string $sUri
	 * @param \WikiPage $oWikiPage
	 * @return array
	 */
	public function getDataConfig( $sUri, $oWikiPage ) {
		$aDC = $this->oDecoratedDP->getDataConfig( $sUri, $oWikiPage );

		$aDC = array_merge( $aDC, parent::getDataConfig( $sUri, $oWikiPage ) );

		$aDC = array_merge( $aDC, [
			'smwproperty' => $this->getSemanticData( $oWikiPage )
		] );

		return $aDC;
	}

	/**
	 * Gets all semantic properties for a given page
	 * and returns correctly formatted array of values to be indexed
	 *
	 * @param \WikiPage $oWikiPage
	 * @return array
	 */
	protected function getSemanticData( $oWikiPage ) {
		$subject = \SMW\DIWikiPage::newFromTitle( $oWikiPage->getTitle() );
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

			$parsedValues = [];
			foreach( $values as $value ) {
				$parsedValues[] = $this->parseSemanticValue( $value );
			}

			if( count( $parsedValues ) == 1 ) {
				//No need to store array for single value
				$parsedValues = $parsedValues[0];
			}

			$smwData[] = [
				"name" => $label,
				"value" => $parsedValues
			];
		}
		return $smwData;
	}

	/**
	 *
	 * @param \SMWDataItem $value
	 * @return string
	 */
	protected function parseSemanticValue( $value ) {
		if( $value instanceof \SMW\DIWikiPage ) {
			return $value->getTitle()->getPrefixedText();
		} else if( $value instanceof \SMWDINumber ) {
			return $value->getNumber();
		} else if( $value instanceof \SMWDIBoolean ) {
			//Default serilization will return "t"/"f"
			//what is the best way to index this?
			return $value->getBoolean() ? 1 : 0;
		} else if( $value instanceof \SMWDITime ) {
			return $value->getMwTimestamp( TS_ISO_8601 );
		} else {
			//Use default serialization
			return $value->__toString();
		}
	}
}
