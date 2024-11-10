<?php

namespace BlueSpice\SMWConnector\ResourceModule;

use MediaWiki\ResourceLoader\Context;
use MediaWiki\ResourceLoader\FileModule;

class SMWApiModule extends FileModule {

	/**
	 * @inheritDoc
	 */
	public function getDependencies( ?Context $context = null ) {
		$dependencies = parent::getDependencies( $context );

		if ( !defined( 'MW_PHPUNIT_TEST' ) && !defined( 'MW_QUIBBLE_CI' ) ) {
			$dependencies[] = 'ext.smw.api';
		}

		return $dependencies;
	}
}
