<?php

namespace BlueSpice\SMWConnector\SecondaryDataUpdate;

use BlueSpice\SecondaryDataUpdate;
use Content;
use DeferredUpdates;
use LinksUpdate;
use MediaWiki\MediaWikiServices;
use Status;
use Title;
use WikiPage;

class SMWOptFoceUpdate extends SecondaryDataUpdate {

	/**
	 * @param Title $title
	 * @param WikiPage $wikiPage
	 * @param Content $content
	 * @return Status
	 */
	protected function doRun( Title $title, WikiPage $wikiPage, Content $content ) {
		$contentRenderer = MediaWikiServices::getInstance()->getContentRenderer();
		$parserOutput = $contentRenderer->getParserOutput( $content, $title );
		$parserOutput->setExtensionData( 'smw:opt.forced.update', true );
		$forcedLinksUpdate = new LinksUpdate( $title, $parserOutput );
		DeferredUpdates::addUpdate( $forcedLinksUpdate );
		return Status::newGood();
	}

}
