<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\LookupModifier;

use BS\ExtendedSearch\Source\LookupModifier\LookupModifier;

class AddSMWAggregation extends LookupModifier {

	public function apply() {
		$this->lookup['aggs']['smwproperty'] = [
			"nested" => [
				"path" => "smwproperty"
			],
			"aggs" => [
				"name" => [
					"terms" => [
						"field" => "smwproperty.name",
						// max num of different SMW props to return - should be tweaked
						"size" => 50,
					],
					"aggs" => [
						"value" => [
							"terms" => [
								"field" => "smwproperty.value",
								"size" => 50
							],
							"aggs" => [
								"type" => [
									"terms" => [
										"field" => "smwproperty.type",
										"size" => 1
									]
								]
							]
						]
					]
				]
			]
		];
	}

	/**
	 * Remove any sensitive Lookup parts previously added
	 * by this modifier, in case they should not be sent to client
	 */
	public function undo() {
		if ( !isset( $this->lookup['aggs'] ) ) {
			return;
		}
		unset( $this->lookup['aggs']['smwproperty'] );
	}
}
