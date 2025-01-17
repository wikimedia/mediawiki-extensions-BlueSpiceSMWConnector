<?php

namespace BlueSpice\SMWConnector\HookHandler;

use MediaWiki\Deferred\LinksUpdate\LinksUpdate;
use MediaWiki\Revision\RenderedRevision;
use MediaWiki\Storage\Hook\RevisionDataUpdatesHook;
use MediaWiki\Title\Title;
use MWException;

class ForceLinksUpdates implements RevisionDataUpdatesHook {

	/** @var array */
	private $alreadyHandeledTitles = [];

	/**
	 * @param Title $title
	 * @param RenderedRevision $renderedRevision
	 * @param array &$updates
	 *
	 * @return void
	 * @throws MWException
	 */
	public function onRevisionDataUpdates( $title, $renderedRevision, &$updates ) {
		$titleDBkey = $title->getPrefixedDBkey();
		$parserOutput = $renderedRevision->getRevisionParserOutput();
		if ( isset( $this->alreadyHandeledTitles[$titleDBkey] ) ) {
			return;
		}
		$parserOutput->setExtensionData( 'smw:opt.forced.update', true );
		$updates[] = new LinksUpdate( $title, $parserOutput );
		$this->alreadyHandeledTitles[$titleDBkey] = true;
	}
}
