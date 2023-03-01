<?php

namespace BlueSpice\SMWConnector\HookHandler;

use MediaWiki\Deferred\LinksUpdate\LinksUpdate;
use MediaWiki\Revision\RenderedRevision;
use MediaWiki\Storage\Hook\RevisionDataUpdatesHook;
use MWException;
use Title;

class ForceLinksUpdates implements RevisionDataUpdatesHook {

	/**
	 * @param Title $title
	 * @param RenderedRevision $renderedRevision
	 * @param array &$updates
	 *
	 * @return void
	 * @throws MWException
	 */
	public function onRevisionDataUpdates( $title, $renderedRevision, &$updates ) {
		$renderedRevision->getRevisionParserOutput()->setExtensionData( 'smw:opt.forced.update', true );
		$updates[] = new LinksUpdate( $title, $renderedRevision->getRevisionParserOutput() );
	}
}
