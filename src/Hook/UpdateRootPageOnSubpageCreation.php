<?php

namespace BlueSpice\SMWConnector\Hook;

use ManualLogEntry;
use MediaWiki\Hook\PageMoveCompleteHook;
use MediaWiki\Page\Hook\PageDeleteCompleteHook;
use MediaWiki\Page\ProperPageIdentity;
use MediaWiki\Permissions\Authority;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\Hook\PageSaveCompleteHook;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use SMW\MediaWiki\Jobs\UpdateJob;
use SMW\Services\ServicesFactory;

class UpdateRootPageOnSubpageCreation implements
	PageSaveCompleteHook,
	PageDeleteCompleteHook,
	PageMoveCompleteHook
{

	/**
	 * @param TitleFactory $titleFactory
	 */
	public function __construct(
		private readonly TitleFactory $titleFactory
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function onPageDeleteComplete(
		ProperPageIdentity $page, Authority $deleter, string $reason, int $pageID, RevisionRecord $deletedRev,
		ManualLogEntry $logEntry, int $archivedRevisionCount
	) {
		$title = $this->titleFactory->castFromPageIdentity( $page );
		$this->updateIfSubpage( $title );
	}

	/**
	 * @inheritDoc
	 */
	public function onPageMoveComplete( $old, $new, $user, $pageid, $redirid, $reason, $revision ) {
		$this->updateIfSubpage( $this->titleFactory->newFromLinkTarget( $old ) );
		$this->updateIfSubpage( $this->titleFactory->newFromLinkTarget( $new ) );
	}

	/**
	 * @inheritDoc
	 */
	public function onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult ) {
		if ( $flags & EDIT_NEW ) {
			$this->updateIfSubpage( $wikiPage->getTitle() );
		}
	}

	/**
	 * @param Title|null $title
	 * @return void
	 */
	private function updateIfSubpage( ?Title $title ): void {
		if ( !$title || !$title->isSubpage() ) {
			return;
		}
		if ( !class_exists( ServicesFactory::class ) ) {
			return;
		}
		$root = $title->getBaseTitle();
		if ( $root ) {
			$job = ServicesFactory::getInstance()->newJobFactory()->newUpdateJob(
				$root,
				[
					UpdateJob::FORCED_UPDATE => true,
					'origin' => 'smw-connector-subpage-modified',
				]
			);
			$job->insert();
		}
	}
}
