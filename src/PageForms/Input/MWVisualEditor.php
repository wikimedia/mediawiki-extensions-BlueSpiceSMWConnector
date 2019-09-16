<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

use PFTextAreaInput;

class MWVisualEditor extends PFTextAreaInput {

	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );

		$this->addJsInitFunctionData( 'bs_smwc_pf_mw_visualeditor_init', $this->getInitParams() );
		$this->addJsValidationFunctionData( 'bs_smwc_pf_mw_visualeditor_validate', $this->getInitParams() );
	}

	public static function getName() {
		return 'bs-mwvisualeditor';
	}

	public function getHtmlText() {
		$html = \Html::openElement(
			'div',
			[
				'id' => 'input_' . $this->mInputNumber . '_cnt',
				'class' => 'bs-mwvisualeditor-field-container',
			]
			+
			$this->makeDataAttributes()
		);
		$html .= \Html::input(
			$this->mInputName,
			$this->mCurrentValue,
			'hidden',
			[
				'id' => 'input_' . $this->mInputNumber
			]
		);
		$html .= \Html::closeElement( 'div' );

		return $html;
	}

	public function getResourceModuleNames() {
		$modules = parent::getResourceModuleNames();
		$modules[] = 'ext.BSSMWConnector.PF.Inputs.MWVisualEditor';

		return $modules;
	}

	public static function getParameters() {
		$params = parent::getParameters();
		return $params;
	}

	protected function makeDataAttributes() {
		return [];
	}

	protected function getInitParams() {
		$params = [
			'input_name' => $this->mInputName,
			'current_value' => $this->mCurrentValue
		];
		return $params;
	}
}
