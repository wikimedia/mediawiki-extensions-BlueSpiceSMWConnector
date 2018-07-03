<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source;

use BS\ExtendedSearch\Source\WikiPages;
use BS\ExtendedSearch\Source\LookupModifier\Base as LookupModifier;
use BlueSpice\SMWConnector\ExtendedSearch\Source;
use BlueSpice\SMWConnector\ExtendedSearch\Source\LookupModifier\AddSourceFields;
use BlueSpice\SMWConnector\ExtendedSearch\Source\LookupModifier\AddSMWAggregation;
use BlueSpice\SMWConnector\ExtendedSearch\Source\LookupModifier\ParseSMWFilters;
use BlueSpice\SMWConnector\ExtendedSearch\Source\Formatter\SMWWikiPageFormatter;

class SMWWikiPage extends WikiPages {
	/**
	 *
	 * @return \BlueSpice\SMWConnector\ExtendedSearch\Source\ExtendedSearch\DocumentProvider\SMWWikiPage
	 */
	public function getDocumentProvider() {
		return new Source\DocumentProvider\SMWWikiPage(
			$this->oDecoratedSource->getDocumentProvider()
		);
	}

	/**
	 *
	 * @return \BlueSpice\SMWConnector\ExtendedSearch\Source\ExtendedSearch\MappingProvider\SMWWikiPage
	 */
	public function getMappingProvider() {
		return new Source\MappingProvider\SMWWikiPage(
			$this->oDecoratedSource->getMappingProvider()
		);
	}

	public function getLookupModifiers( $oLookup, $oContext, $sType = LookupModifier::TYPE_SEARCH ) {
		$lookupModifiers = parent::getLookupModifiers( $oLookup, $oContext, $sType );

		if( $sType === LookupModifier::TYPE_SEARCH ) {
			$lookupModifiers['smw-source-fields'] = new AddSourceFields( $oLookup, $oContext );
			$lookupModifiers['smw-aggregation'] = new AddSMWAggregation( $oLookup, $oContext );
			$lookupModifiers['smw-filter-parser'] = new ParseSMWFilters( $oLookup, $oContext );
		}
		return $lookupModifiers;
	}

	public function getFormatter() {
		return new SMWWikiPageFormatter( $this );
	}
}
