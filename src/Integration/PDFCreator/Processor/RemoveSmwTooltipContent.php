<?php

namespace BlueSpice\SMWConnector\Integration\PDFCreator\Processor;

use DOMXPath;
use MediaWiki\Extension\PDFCreator\IProcessor;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Extension\PDFCreator\Utility\ExportPage;

class RemoveSmwTooltipContent implements IProcessor {

	/**
	 * @inheritDoc
	 */
	public function execute(
		array &$pages,
		array &$images,
		array &$attachments,
		ExportContext $context,
		string $module = '',
		$params = []
	): void {
		/** @var ExportPage $page */
		foreach ( $pages as &$page ) {
			$dom = $page->getDOMDocument();
			$xpath = new DOMXPath( $dom );
			$tooltipNodes = $xpath->query(
				'//*[contains(concat(" ", normalize-space(@class), " "), " smwttcontent ")]'
			);

			if ( !$tooltipNodes || $tooltipNodes->length === 0 ) {
				return;
			}

			foreach ( iterator_to_array( $tooltipNodes ) as $node ) {
				if ( $node->parentNode ) {
					$node->parentNode->removeChild( $node );
				}
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getPosition(): int {
		return 50;
	}
}
