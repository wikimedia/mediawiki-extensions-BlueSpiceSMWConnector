<?php

namespace BlueSpice\SMWConnector\Data\TreeAsk;

use MediaWiki\Title\Title;
use MWStake\MediaWiki\Component\DataStore\Record;

class SecondaryDataProvider extends \BlueSpice\SMWConnector\Data\Ask\SecondaryDataProvider {

	/**
	 * Allow sub-classes to add fields based on title without having to
	 * re-initialize it
	 *
	 * @param Title $title
	 * @param Record &$record
	 */
	protected function addFieldsForTitle( Title $title, &$record ) {
		$record->set( 'label', $record->get( 'page' ) );
		$record->set( 'page_url', $title->getLocalURL() );
		$record->set( 'leaf', false );
	}
}
