<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source;

use BlueSpice\SMWConnector\ExtendedSearch\Source;
use BlueSpice\SMWConnector\ExtendedSearch\Source\Formatter\SMWWikiPageFormatter;
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
	 * @return SMWWikiPageFormatter
	 */
	public function getFormatter() {
		return new SMWWikiPageFormatter( $this );
	}
}
