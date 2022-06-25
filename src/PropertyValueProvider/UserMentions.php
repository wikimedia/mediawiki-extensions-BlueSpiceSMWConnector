<?php

namespace BlueSpice\SMWConnector\PropertyValueProvider;

use BlueSpice\SMWConnector\PropertyValueProvider;
use MediaWiki\MediaWikiServices;
use SESP\AppFactory;
use SMW\DIProperty;
use SMW\SemanticData;
use SMWDataItem;
use SMWDIWikiPage;
use Title;
use WikiPage;
use WikitextContent;

class UserMentions extends PropertyValueProvider {

	/**
	 *
	 * @return string
	 */
	public function getAliasMessageKey() {
		return "bs-smwconnector-propertyvalueprovider-usermentions-alias";
	}

	/**
	 *
	 * @return string
	 */
	public function getDescriptionMessageKey() {
		return "bs-smwconnector-propertyvalueprovider-usermentions-desc";
	}

	/**
	 *
	 * @return int
	 */
	public function getType() {
		return SMWDataItem::TYPE_WIKIPAGE;
	}

	/**
	 *
	 * @return string
	 */
	public function getId() {
		return '_USERMENTIONS';
	}

	/**
	 *
	 * @return string
	 */
	public function getLabel() {
		return "Content/User mentions";
	}

	/**
	 * @param AppFactory $appFactory
	 * @param DIProperty $property
	 * @param SemanticData $semanticData
	 */
	public function addAnnotation( $appFactory, $property, $semanticData ) {
		$wikiPage = MediaWikiServices::getInstance()->getWikiPageFactory()
			->newFromTitle( $semanticData->getSubject()->getTitle() );
		if ( !$wikiPage instanceof WikiPage ) {
			// no file or category pages
			return;
		}
		$content = $wikiPage->getContent();
		if ( !$content instanceof WikitextContent ) {
			// do not do this for any other type of content as these may do not
			// support links
			return;
		}
		$links = $content->getParserOutput( $semanticData->getSubject()->getTitle() )->getLinks();
		if ( empty( $links[NS_USER] ) ) {
			return;
		}
		foreach ( $links[NS_USER] as $name => $id ) {
			$userPage = Title::makeTitle( NS_USER, $name );
			$semanticData->addPropertyObjectValue(
				$property,
				SMWDIWikiPage::newFromTitle( $userPage )
			);
		}
	}

}
