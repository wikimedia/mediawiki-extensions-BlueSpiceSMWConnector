<?php

namespace BlueSpice\SMWConnector\BreadcrumbDataProvider;

use BlueSpice\Discovery\BreadcrumbDataProvider\BaseBreadcrumbDataProvider;
use MediaWiki\Context\RequestContext;
use MediaWiki\Title\Title;
use SMW\Encoder;

class SpecialBrowseProvider extends BaseBreadcrumbDataProvider {

	/**
	 * @param Title $title
	 * @return Title
	 */
	public function getRelevantTitle( $title ): Title {
		$bits = explode( '/', $title->getText() );
		if ( count( $bits ) === 1 ) {
			return $title;
		}
		$pagename = array_pop( $bits );
		if ( class_exists( Encoder::class ) ) {
			$decoded = Encoder::decode( $pagename );
			if ( $decoded ) {
				$pagename = $decoded;
			}
		}

		// Page title is set in article param if accessed via Special:Browse
		$article = RequestContext::getMain()->getRequest()->getText( 'article', '' );
		if ( !empty( $article ) ) {
			$pagename = $article;
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

	/**
	 * @param Title $title
	 * @return array
	 */
	public function getNodes( Title $title ): array {
		$parentNodes = parent::getNodes( $title );
		$nodes = [];
		foreach ( $parentNodes as $node ) {
			if ( isset( $node['current'] ) ) {
				unset( $node['current'] );
			}
			$nodes[] = $node;
		}

		return $nodes;
	}
}
