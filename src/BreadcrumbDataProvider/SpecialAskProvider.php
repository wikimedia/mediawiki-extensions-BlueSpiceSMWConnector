<?php

namespace BlueSpice\SMWConnector\BreadcrumbDataProvider;

use BlueSpice\Discovery\BreadcrumbDataProvider;
use MediaWiki\SpecialPage\SpecialPageFactory;
use Title;

class SpecialAskProvider extends BreadcrumbDataProvider {

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
	 */
	public function getRelevantTitle( $title ): Title {
		return $this->specialPageFactory->getPage( 'Ask' )->getPageTitle();
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
