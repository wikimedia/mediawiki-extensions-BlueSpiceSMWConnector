<?php

namespace BlueSpice\SMWConnector\BreadcrumbDataProvider;

use BlueSpice\Discovery\BreadcrumbDataProvider\BaseBreadcrumbDataProvider;
use Title;

class SpecialBrowseProvider extends BaseBreadcrumbDataProvider {

	/**
	 * @param Title $title
	 * @return Title
	 */
	public function getRelevantTitle( $title ): Title {
		$bits = explode( '/', $title->getText() );
		$pagename = array_pop( $bits );
		if ( class_exists( \SMW\Encoder::class ) ) {
			$decoded = \SMW\Encoder::decode( $pagename );
			if ( $decoded ) {
				$pagename = $decoded;
			}
		}
		return $this->titleFactory->newFromText( $pagename );
	}

	/**
	 * @param Title $title
	 * @return array
	 */
	public function getLabels( $title ): array {
		return [
			'text' => $this->messageLocalizer->msg( 'browse' )
		];
	}

	/**
	 * @param Title $title
	 * @return bool
	 */
	public function applies( Title $title ): bool {
		return $title->isSpecial( 'Browse' );
	}
}
