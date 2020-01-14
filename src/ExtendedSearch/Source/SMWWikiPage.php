<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source;

use BlueSpice\SMWConnector\ExtendedSearch\Source;
use BlueSpice\SMWConnector\ExtendedSearch\Source\Formatter\SMWWikiPageFormatter;
use BlueSpice\SMWConnector\ExtendedSearch\Source\LookupModifier\AddSMWAggregation;
use BlueSpice\SMWConnector\ExtendedSearch\Source\LookupModifier\AddSourceFields;
use BlueSpice\SMWConnector\ExtendedSearch\Source\LookupModifier\ParseSMWFilters;
use BS\ExtendedSearch\Source\LookupModifier\Base as LookupModifier;
use BS\ExtendedSearch\Source\WikiPages;

class SMWWikiPage extends WikiPages {
	/**
	 *
	 * @return DocumentProvider\SMWWikiPage
	 */
	public function getDocumentProvider() {
		return new Source\DocumentProvider\SMWWikiPage(
			$this->oDecoratedSource->getDocumentProvider()
		);
	}

	/**
	 * @param Base $base
	 * @return SMWWikiPage
	 */
	public static function create( $base ) {
		return new self( $base );
	}

	/**
	 *
	 * @return MappingProvider\SMWWikiPage
	 */
	public function getMappingProvider() {
		return new Source\MappingProvider\SMWWikiPage(
			$this->oDecoratedSource->getMappingProvider()
		);
	}

	/**
	 *
	 * @param \BS\ExtendedSearch\Lookup $oLookup
	 * @param \IContextSource $oContext
	 * @param string $sType
	 * @return ParseSMWFilters
	 */
	public function getLookupModifiers( $oLookup, $oContext, $sType = LookupModifier::TYPE_SEARCH ) {
		$lookupModifiers = parent::getLookupModifiers( $oLookup, $oContext, $sType );

		if ( $sType === LookupModifier::TYPE_SEARCH ) {
			$lookupModifiers['smw-source-fields'] = new AddSourceFields( $oLookup, $oContext );
			$lookupModifiers['smw-aggregation'] = new AddSMWAggregation( $oLookup, $oContext );
			$lookupModifiers['smw-filter-parser'] = new ParseSMWFilters( $oLookup, $oContext );
		}
		return $lookupModifiers;
	}

	/**
	 *
	 * @return SMWWikiPageFormatter
	 */
	public function getFormatter() {
		return new SMWWikiPageFormatter( $this );
	}
}
