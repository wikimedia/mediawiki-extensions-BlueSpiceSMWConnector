<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\MappingProvider;
use BS\ExtendedSearch\Source\MappingProvider\WikiPage;

class SMWWikiPage extends WikiPage {
	public function getPropertyConfig() {
		$aPC = $this->oDecoratedMP->getPropertyConfig();
		$aPC = array_merge( $aPC, parent::getPropertyConfig() );

		$aPC = array_merge( $aPC, [
			'smwproperty' => [
				'type' => 'nested',
				'properties' => [
					'name' => [
						'type' => 'keyword'
					],
					'value' => [
						'type' => 'keyword'
					],
					'type' => [
						'type' => 'keyword'
					]
				]
			],
			'smwproperty_agg' => [
				'type' => 'keyword'
			]
		] );

		return $aPC;
	}
}
