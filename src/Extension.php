<?php

namespace BlueSpice\SMWConnector;

class Extension extends \BlueSpice\Extension {

	/**
	 * Register new result format
	 */
	public static function setup() {
		$GLOBALS['smwgResultFormats']['bsgrid'] = 'BSGridResultPrinter';
	}
}
