<?php

namespace BlueSpice\SMWConnector\HookHandler;

use BlueSpice\SmartList\BlueSpiceSmartListModeFactory;
use BlueSpice\SMWConnector\Tag\DataQuery;
use BlueSpice\SMWConnector\Tag\DecisionOverview;
use MWStake\MediaWiki\Component\GenericTagHandler\Hook\MWStakeGenericTagHandlerInitTagsHook;

class RegisterTags implements MWStakeGenericTagHandlerInitTagsHook {

	/**
	 * @param BlueSpiceSmartListModeFactory $modeFactory
	 */
	public function __construct(
		private readonly BlueSpiceSmartListModeFactory $modeFactory
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function onMWStakeGenericTagHandlerInitTags( array &$tags ) {
		$tags[] = new DataQuery( $this->modeFactory->createMode( 'dataquery' ) );
		$tags[] = new DecisionOverview();
	}
}
