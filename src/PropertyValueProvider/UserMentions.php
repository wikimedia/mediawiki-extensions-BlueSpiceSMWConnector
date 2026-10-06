<?php

namespace BlueSpice\SMWConnector\PropertyValueProvider;

use BlueSpice\SMWConnector\PropertyValueProvider;
use MediaWiki\Content\WikitextContent;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOutputLinkTypes;
use MediaWiki\Title\Title;
use SESP\AppFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SemanticData;
use SMWDataItem;

class UserMentions extends PropertyValueProvider {

	/**
	 * @return string
	 */
	public function getAliasMessageKey() {
		return "bs-smwconnector-propertyvalueprovider-usermentions-alias";
	}

	/**
	 * @return string
	 */
	public function getDescriptionMessageKey() {
		return "bs-smwconnector-propertyvalueprovider-usermentions-desc";
	}

	/**
	 * @return int
	 */
	public function getType() {
		return SMWDataItem::TYPE_WIKIPAGE;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return '_USERMENTIONS';
	}

	/**
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
		$content = $wikiPage->getContent();
		if ( !$content instanceof WikitextContent ) {
			// do not do this for any other type of content as these may do not
			// support links
			return;
		}
		$contentRenderer = MediaWikiServices::getInstance()->getContentRenderer();
		$links = $contentRenderer->getParserOutput( $content, $semanticData->getSubject()->getTitle() )
			->getLinkList( ParserOutputLinkTypes::LOCAL, NS_USER );
		if ( empty( $links ) ) {
			return;
		}
		foreach ( $links as $link ) {
			if ( $link['pageid'] ) {
				$userPage = Title::newFromID( $link['pageid'] );
				if ( $userPage ) {
					$semanticData->addPropertyObjectValue(
						$property,
						DIWikiPage::newFromTitle( $userPage )
					);
				}
			}
		}
	}

}
