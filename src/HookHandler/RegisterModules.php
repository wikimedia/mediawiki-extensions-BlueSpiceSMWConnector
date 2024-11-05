<?php

namespace BlueSpice\SMWConnector\HookHandler;

use MediaWiki\ResourceLoader\Hook\ResourceLoaderRegisterModulesHook;
use MediaWiki\ResourceLoader\ResourceLoader;

class RegisterModules implements ResourceLoaderRegisterModulesHook {

	/**
	 * @inheritDoc
	 */
	public function onResourceLoaderRegisterModules( ResourceLoader $resourceLoader ): void {
		if ( defined( 'MW_PHPUNIT_TEST' ) || defined( 'MW_QUIBBLE_CI' ) ) {
			$resourceLoader->register( 'ext.smw.api', [] );
		}
	}
}
