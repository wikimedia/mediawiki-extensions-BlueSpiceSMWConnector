<?php

namespace BlueSpice\SMWConnector\Tag;

use BlueSpice\SmartList\Tag\SmartlistTag;
use MediaWiki\Language\RawMessage;
use MediaWiki\Message\Message;
use MWStake\MediaWiki\Component\FormEngine\FormLoaderSpecification;
use MWStake\MediaWiki\Component\GenericTagHandler\ClientTagSpecification;

class DataQuery extends SmartlistTag {

	/**
	 * @inheritDoc
	 */
	public function getTagNames(): array {
		return [ 'dataquery' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getClientTagSpecification(): ClientTagSpecification|null {
		return new ClientTagSpecification(
			'DataQuery',
			new RawMessage( '' ),
			new FormLoaderSpecification(
				'bs.swmconnector.ui.DataQueryForm',
				[ 'ext.BSSMWConnector.dataquery.form' ]
			),
			Message::newFromKey( 'bs-smwconnector-dataquery-name' )
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getResourceLoaderModules(): ?array {
		return [ 'ext.BSSMWConnector.dataquery.tag' ];
	}
}
