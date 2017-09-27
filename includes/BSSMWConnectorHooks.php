<?php

class BSSMWConnectorHooks {

	/**
	 * Add VisualEditor standard config to Special:FormEdit this is necessary
	 * as VisualEditor itself only adds in 'edit' mode!
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return boolean Always true
	 */
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModuleStyles( 'ext.BSSMWConnector.styles' );

		if( !$out->getTitle()->isSpecial( 'FormEdit') && $out->getRequest()->getVal('action', 'view') !== 'formedit' ) {
			return true;
		}

		$out->addModules( 'ext.BSSMWConnector.PageForms.DateTimePicker.fix' );

		$oVE = BsExtensionManager::getExtension( 'VisualEditor' );
		if( $oVE instanceof BlueSpiceVisualEditor ) {
			global $wgParser;
			$aConfigs = $oVE->makeConfig( $wgParser );
		}
		else {
			return true;
		}

		$out->addJsConfigVars( 'BsVisualEditorConfigDefault', $aConfigs['standard'] );
		$out->addJsConfigVars( 'BsVisualEditorLoaderUsingDeps', $aConfigs['module_deps'] );

		//Semantic Forms does not load the messages from modules returned by SFInput::getResourceModuleNames
		//$out->addModuleMessages( 'ext.bluespice.visualEditor.tinymce' );
		$out->addModules( array(
			'ext.bluespice.visualEditor.styles',
			'ext.bluespice.visualEditor.tinymce',
			'ext.BSSMWConnector.SF.VisualEditorField',
			'ext.BSSMWConnector.SF.FreeTextVisualEditor'
		) );

		//This is ugly, but as long as the "Insert*" extensions can not detect
		//the precence of VE or the "form edit" action we need to load those
		//manually
		global $bsgExtendedEditBarEnabledActions;
		$bsgExtendedEditBarEnabledActions[] = 'formedit';
		$bsgExtendedEditBarEnabledActions[] = 'view';
		$oEEB = BsExtensionManager::getExtension('ExtendedEditBar');
		if( $oEEB instanceof ExtendedEditBar ) {
			$dummy = '<div></div>'; //Must not be empty string
			$oEEB->onEditPageBeforeEditToolbar( $dummy );
		}

		return true;
	}

	/**
	 * Registers new input types
	 * @param PFFormPrinter $formPrinter
	 * @return boolean Always true
	 */
	public static function onPFFormPrinterSetup( PFFormPrinter $formPrinter ) {
		$formPrinter->registerInputType( 'BSSFVisualEditor' );
		return true;
	}

	/**
	 *
	 * @param string $sType The type of the node that gets processed
	 * @param string $sNodeText HTML element value to be rendered (RAW)
	 * @param array $aAttribs HTML attributes to be rendered
	 * @param string $sElement HTML element name to be rendered
	 * @param Parser $oParser
	 * @return boolean Always true to keep hook running
	 */
	public static function onBSBookshelfNodeTag( $sType, &$sNodeText, &$aAttribs, &$sElement, $oParser ) {
		if( $sType !== 'ask' ) {
			return true;
		}

		$aAttribs['class'] .= ' bs-smw-node-ask';

		return true;
	}

	/**
	 *
	 * @param array $aDummyPage
	 * @param array $aArticle
	 * @param array $aTemplate
	 * @param DOMElement $oTOCList
	 * @param array $aBookMeta
	 * @param array $aLinkMap
	 * @param array $aBookPage
	 * @param DOMXPath $oDOMXPath
	 * @return boolean Always true to keep hook running
	 */
	public static function onBSBookshelfExportTag( &$aDummyPage, &$aArticle, &$aTemplate, $oTOCList, $aBookMeta, &$aLinkMap, &$aBookPage, $oDOMXPath ) {
		if( !isset($aArticle['bookshelf']) || !isset($aArticle['bookshelf']['arguments']['type']) ) {
			return true;
		}
		if( strtolower( $aArticle['bookshelf']['arguments']['type'] ) !== 'ask' ) {
			return true;
		}

		$aParams = new DerivativeRequest(
			RequestContext::getMain()->getRequest(),
			array(
				'action' => 'askargs',
				'conditions' => $aArticle['bookshelf']['arguments']['conditions'],
				'printouts' => '',
				'parameters' => ''
			),
			true
		);

		$oApi = new ApiMain( $aParams );
		$oApi->execute();

		$data = $oApi->getResult()->getResultData();
		if( !isset( $data['query']['results'] ) || empty( $data['query']['results'] ) ) {
			return true;
		}

		//If there is no table of contents yet, we create one and add it to the '.bodyContent'
		if( $aDummyPage['toc-ul-element'] instanceof DOMElement === false ) {
			$sId = str_replace( '#bs-ue-jumpmark-', '', $aDummyPage['bookmark-element']->getAttribute( 'href' ) );
			$sTocHeading = wfMessage( 'toc' )->plain();
			$oTOCDOM = new DOMDocument();
			$oTOCDOM->loadXML(<<<HERE
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
				->getElementsByTagName('ul')->item(0);
		}

		$iCount = 0;
		foreach( $data['query']['results'] as $sTitle => $aData ) {
			$iCount++;
			$aPage = BsPDFPageProvider::getPage( array( 'title' => $sTitle ) );

			//Let's get the bookmark XML from the page and add it to the TagNode's bookmark XML
			$oBookmark = BsUniversalExportHelper::getBookmarkElementForPageDOM($aPage['dom']);
			$aDummyPage['bookmark-element']->appendChild(
				$aDummyPage['bookmarks-dom']->importNode( $oBookmark, true )
			);

			//Also append the TagNode's ToC
			$oToCItem = $aDummyPage['dom']->createElement('li');
			$oToCItem->setAttribute( 'class', 'toclevel-1 tocsection-1' );
			$oToCEntryLink = $aDummyPage['dom']->createDocumentFragment();
			$aTocName = $oBookmark->getAttribute( 'name' );
			$aTocHref = $oBookmark->getAttribute( 'href' );
			$oToCEntryLink->appendXML(<<<HERE
<a href="$aTocHref">
	<span class="tocnumber">$iCount</span> <span class="toctext">$aTocName</span>
</a>
HERE
			);
			$oToCItem->appendChild( $oToCEntryLink );

			$aDummyPage['toc-ul-element']->appendChild($oToCItem);
			if( $aPage['toc-ul-element'] instanceof DOMElement ) {
				$oToCItem->appendChild(
					$aDummyPage['dom']->importNode( $aPage['toc-ul-element'], true )
				);
				//TODO: Maybe remove original ToC from $aPage['dom']?
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
	 * @param WikiPage $article
	 * @param User $user
	 * @param Content $content
	 * @param string $summary
	 * @param boolean $isMinor
	 * @param boolean $isWatch
	 * @param int $section
	 * @param int $flags
	 * @param int $revision
	 * @param Status $status
	 * @param int $baseRevId
	 * @return boolean Always true to keep hook running
	 */
	public static function onPageContentSaveComplete( $article, $user, $content,
			$summary, $isMinor, $isWatch, $section, $flags, $revision, $status,
			$baseRevId ) {

		DataUpdate::runUpdates(
			$content->getSecondaryDataUpdates( $article->getTitle() )
		);
		return true;
	}
}