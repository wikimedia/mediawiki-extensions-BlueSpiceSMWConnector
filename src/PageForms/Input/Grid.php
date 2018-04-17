<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

class Grid extends \PFFormInput {

	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );

		$this->addJsInitFunctionData( 'bs.smwc.pf.input.grid.init' );
	}

	public static function getName() {
		return 'bs-grid';
	}

	public function getHtmlText() {
		return static::class;
	}

	public function getResourceModuleNames() {
		return [
			'ext.BSSMWConnector.PF.Input.UserGrid'
		];
	}
}