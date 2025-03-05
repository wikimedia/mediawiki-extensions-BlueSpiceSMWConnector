<?php

namespace BlueSpice\SMWConnector\Hook\SMWStoreAfterQueryResultLookupComplete;

use BlueSpice\SMWConnector\Hook\SMWStoreAfterQueryResultLookupComplete;
use MediaWiki\MediaWikiServices;
use SMW\DIWikiPage;
use SMW\Query\QueryResult;

class ApplySecurityTrimming extends SMWStoreAfterQueryResultLookupComplete {
	/**
	 * @return bool
	 */
	protected function skipProcessing() {
		return !( $this->result instanceof QueryResult );
	}

	/**
	 *
	 * @var DIWikiPage[]
	 */
	protected $resultItems = [];

	protected function doProcess() {
		$this->resultItems = $this->result->getResults();
		$filteredItems = [];

		$pm = MediaWikiServices::getInstance()->getPermissionManager();
		$user = $this->getContext()->getUser();
		foreach ( $this->resultItems as $wikiPageItem ) {
			$title = $wikiPageItem->getTitle();
			if ( $title === null ) {
				// Leave it in result set
				$filteredItems[] = $wikiPageItem;
				continue;
			}
			if ( !$pm->userCan( 'read', $user, $title ) ) {
				continue;
			}

			$filteredItems[] = $wikiPageItem;
		}

		if ( count( $filteredItems ) !== count( $this->resultItems ) ) {
			$this->result = new QueryResult(
				$this->result->getPrintRequests(),
				$this->result->getQuery(),
				$filteredItems,
				$this->result->getStore(),
				$this->result->hasFurtherResults()
			);
		}

		return true;
	}
}
