<?php

namespace BlueSpice\SMWConnector\Hook\SMWStoreBeforeQueryResultLookupComplete;

use BlueSpice\SMWConnector\Hook\SMWStoreBeforeQueryResultLookupComplete;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use SMW\Query\Language\Conjunction;
use SMW\Query\Language\Description;
use SMW\Query\Language\Disjunction;
use SMW\Query\Language\NamespaceDescription;
use SMW\Query\QueryResult;

class AddPermissionBasedNamespaceFilters extends SMWStoreBeforeQueryResultLookupComplete {

	/**
	 *
	 * @var Description
	 */
	protected $originalDescription = null;

	protected function doProcess() {
		$namespaceFilterDisjunction = $this->makeNamespaceFilterDisjunction();
		$this->originalDescription = $this->query->getDescription();

		// The user is not allowed to read in any namespace!?
		if ( $namespaceFilterDisjunction instanceof Disjunction === false ) {
			$this->result = new QueryResult(
				$this->originalDescription->getPrintRequests(),
				$this->query,
				[],
				$this->store
			);
			return false;
		}

		$newDescription = new Conjunction( [
			$this->originalDescription,
			$namespaceFilterDisjunction
		] );

		$this->query->setDescription( $newDescription );

		return true;
	}

	/**
	 * @return Disjunction|null
	 */
	protected function makeNamespaceFilterDisjunction() {
		$readableNamespaceDescriptions = [];
		$namespaceIds = $this->getContext()->getLanguage()->getNamespaceIds();

		$pm = MediaWikiServices::getInstance()->getPermissionManager();
		$user = $this->getContext()->getUser();
		foreach ( $namespaceIds as $nmspText => $namespaceId ) {
			$dummyTitle = Title::makeTitle( $namespaceId, 'X' );
			if ( $pm->userCan( 'read', $user, $dummyTitle ) ) {
				$readableNamespaceDescriptions[] = new NamespaceDescription( $namespaceId );
			}
		}

		if ( empty( $readableNamespaceDescriptions ) ) {
			return null;
		}

		return new Disjunction( $readableNamespaceDescriptions );
	}
}
