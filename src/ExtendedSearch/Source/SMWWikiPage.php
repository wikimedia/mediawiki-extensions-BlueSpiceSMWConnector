<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source;

use BS\ExtendedSearch\Source\WikiPages;
use BlueSpice\SMWConnector\ExtendedSearch\Source;

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
}
