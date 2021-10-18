<?php

use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;

class BSSMWConnectorHooks {

	/**
	 * Add VisualEditor standard config to Special:FormEdit this is necessary
	 * as VisualEditor itself only adds in 'edit' mode!
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool Always true
	 */
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModules( 'ext.BSSMWConnector' );

		if ( $out->getTitle()->isSpecial( 'BookshelfBookUI' ) ) {
			$out->addModules( 'ext.BSSMWConnector.BookshelfUI' );
		}

		if ( !$out->getTitle()->isSpecial( 'FormEdit' )
			&& $out->getRequest()->getVal( 'action', 'view' ) !== 'formedit' ) {
			return true;
		}

		$out->addModules( [
			'ext.BSSMWConnector.PageForms.DateTimePicker.fix',
			'ext.BSSMWConnector.visualEditor'
		] );

		return true;
	}

	/**
	 *
	 * @param array &$aDummyPage
	 * @param array &$aArticle
	 * @param array &$aTemplate
	 * @param DOMElement $oTOCList
	 * @param array $aBookMeta
	 * @param array &$aLinkMap
	 * @param array &$aBookPage
	 * @param DOMXPath $oDOMXPath
	 * @return bool Always true to keep hook running
	 */
	public static function onBSBookshelfExportTag( &$aDummyPage, &$aArticle, &$aTemplate,
		$oTOCList, $aBookMeta, &$aLinkMap, &$aBookPage, $oDOMXPath ) {
		if ( !isset( $aArticle['bookshelf'] )
			|| !isset( $aArticle['bookshelf']['arguments']['type'] ) ) {
			return true;
		}
		if ( strtolower( $aArticle['bookshelf']['arguments']['type'] ) !== 'ask' ) {
			return true;
		}

		$aParams = new DerivativeRequest(
			RequestContext::getMain()->getRequest(),
			[
				'action' => 'askargs',
				'conditions' => $aArticle['bookshelf']['arguments']['conditions'],
				'printouts' => '',
				'parameters' => ''
			],
			true
		);

		$oApi = new ApiMain( $aParams );
		$oApi->execute();

		$data = $oApi->getResult()->getResultData();
		if ( !isset( $data['query']['results'] ) || empty( $data['query']['results'] ) ) {
			return true;
		}

		// If there is no table of contents yet, we create one and add it to the '.bodyContent'
		if ( $aDummyPage['toc-ul-element'] instanceof DOMElement === false ) {
			$sId = str_replace(
				'#bs-ue-jumpmark-',
				'',
				$aDummyPage['bookmark-element']->getAttribute( 'href' )
			);
			$sTocHeading = wfMessage( 'toc' )->plain();
			$oTOCDOM = new DOMDocument();
			$oTOCDOM->loadXML( <<<HERE
<div id="toc-bs-ue-jumpmark-$sId" class="toc">
	<div id="toctitle-bs-ue-jumpmark-$sId" class="toctitle">
		<h2>$sTocHeading</h2>
	</div>
	<ul>
	</ul>
</div>
HERE
			);

			$aDummyPage['bodycontent-element']->insertBefore(
				$aDummyPage['dom']->importNode( $oTOCDOM->documentElement, true ),
				$aDummyPage['bodycontent-element']->firstChild
			);
			$aDummyPage['toc-ul-element'] = $aDummyPage['bodycontent-element']
				->getElementsByTagName( 'ul' )->item( 0 );
		}

		$iCount = 0;
		foreach ( $data['query']['results'] as $sTitle => $aData ) {
			$iCount++;
			$aPage = BsPDFPageProvider::getPage( [ 'title' => $sTitle ] );

			// Let's get the bookmark XML from the page and add it to the TagNode's bookmark XML
			$oBookmark = BsUniversalExportHelper::getBookmarkElementForPageDOM( $aPage['dom'] );
			$aDummyPage['bookmark-element']->appendChild(
				$aDummyPage['bookmarks-dom']->importNode( $oBookmark, true )
			);

			// Also append the TagNode's ToC
			$oToCItem = $aDummyPage['dom']->createElement( 'li' );
			$oToCItem->setAttribute( 'class', 'toclevel-1 tocsection-1' );
			$oToCEntryLink = $aDummyPage['dom']->createDocumentFragment();
			$aTocName = $oBookmark->getAttribute( 'name' );
			$aTocHref = $oBookmark->getAttribute( 'href' );
			$oToCEntryLink->appendXML( <<<HERE
<a href="$aTocHref">
	<span class="tocnumber">$iCount</span> <span class="toctext">$aTocName</span>
</a>
HERE
			);
			$oToCItem->appendChild( $oToCEntryLink );

			$aDummyPage['toc-ul-element']->appendChild( $oToCItem );
			if ( $aPage['toc-ul-element'] instanceof DOMElement ) {
				$oToCItem->appendChild(
					$aDummyPage['dom']->importNode( $aPage['toc-ul-element'], true )
				);
				// TODO: Maybe remove original ToC from $aPage['dom']?
			}

			$aDummyPage['bodycontent-element']->appendChild(
				$aDummyPage['dom']->importNode( $aPage['dom']->documentElement, true )
			);
		}

		return true;
	}

	/**
	 * Since commit [1] the 'categorylinks' table is not updated directly when a
	 * page gets saved. Therefore the "Edit with form" tab of
	 * "Extension:PageForms" may mot be shown directly after page save.
	 * This hook handler updates the 'categorylinks' table.
	 *
	 * [1] https://github.com/wikimedia/mediawiki/commit/072e3666d3fcd1738d4742930bbe3acd5e7519b2#diff-a0f7feeaae57e9d2c735c8919c16ad15R2198
	 *
	 * @param WikiPage $wikiPage
	 * @param MediaWiki\User\UserIdentity $user
	 * @param string $summary
	 * @param int $flags
	 * @param RevisionRecord $revisionRecord
	 * @return bool Always true to keep hook running
	 */
	public static function onPageSaveComplete( WikiPage $wikiPage, $user, $summary, $flags, $revisionRecord ) {
		$content = $revisionRecord->getContent( SlotRecord::MAIN, RevisionRecord::RAW );
		DataUpdate::runUpdates(
			$content->getSecondaryDataUpdates( $wikiPage->getTitle() )
		);
		return true;
	}
}
