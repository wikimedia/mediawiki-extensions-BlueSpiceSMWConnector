<?php

namespace BlueSpice\SMWConnector\Tests;

use BlueSpice\SMWConnector\SmartListMode\DataQueryMode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BlueSpice\SMWConnector\DataQueryMode
 */
class DataQueryModeTest extends TestCase {

	/**
	 * @param string $args
	 * @param string $format
	 * @param string $expectedSMW
	 * @dataProvider provideData
	 * @covers \BlueSpice\SMWConnector\DataQueryMode::createSMWformat
	 */
	public function testCreateSMWformat( $args, $format, $expectedSMW ) {
		$dataQueryMode = new DataQueryMode();
		$actualSMW = $dataQueryMode->createSMWformat( $args, $format );
		$this->assertSame( $expectedSMW, $actualSMW );
	}

	public function provideData() {
		return [
			'categories' => [
				'cat1|cat2',
				'categories',
				'[[Category:cat1]]OR[[Category:cat2]]'
			],
			'namespaces' => [
				'ns1|ns2',
				'namespaces',
				'[[ns1:+]]OR[[ns2:+]]'
			],
			'modified' => [
				'>2023-04-01',
				'modified',
				'[[Modification date::>2023-04-01]]'
			],
			'printouts' => [
				'print1|print2',
				'printouts',
				'|?print1|?print2'
			]
		];
	}
}
