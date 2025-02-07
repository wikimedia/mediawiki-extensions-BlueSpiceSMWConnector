<?php

namespace BlueSpice\SMWConnector\BreadcrumbDataProvider;

use BlueSpice\Discovery\BreadcrumbDataProvider\BaseBreadcrumbDataProvider;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Title\NamespaceInfo;
use MediaWiki\Title\Title;
use MWException;

class SpecialAskProvider extends BaseBreadcrumbDataProvider {

	/** @var SpecialPageFactory */
	private $specialPageFactory;

	/**
	 *
	 * @param SpecialPageFactory $specialPageFactory
	 * @param TitleFactory $titleFactory
	 * @param MessageLocalizer $messageLocalizer
	 * @param WebRequestValues $webRequestValues
	 * @param NamespaceInfo $namespaceInfo
	 */
	public function __construct( $specialPageFactory, $titleFactory,
		$messageLocalizer, $webRequestValues, $namespaceInfo ) {
		parent::__construct( $titleFactory, $messageLocalizer, $webRequestValues, $namespaceInfo );
		$this->specialPageFactory = $specialPageFactory;
	}

	/**
	 * @param Title $title
	 * @return Title
	 * @throws MWException If the "Ask" page doesn't exist
	 */
	public function getRelevantTitle( $title ): Title {
		$specialPage = $this->specialPageFactory->getPage( 'Ask' );
		if ( !$specialPage ) {
			throw new MWException( 'The "Ask" page doesn\'t exist' );
		}
		return $specialPage->getPageTitle();
	}

	/**
	 * @param Title $title
	 * @return array
	 */
	public function getLabels( $title ): array {
		return [];
	}

	/**
	 *
	 * @param Title $title
	 * @return bool
	 */
	public function applies( Title $title ): bool {
		return $title->isSpecial( 'Ask' );
	}
}
