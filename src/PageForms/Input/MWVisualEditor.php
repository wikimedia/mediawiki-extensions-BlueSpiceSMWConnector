<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

use MediaWiki\Html\Html;
use PFTextAreaInput;

class MWVisualEditor extends PFTextAreaInput {

	/**
	 * @param string $input_number The number of the input in the form. For a simple HTML input
	 *  element this should end up in the id attribute in the format 'input_<number>'.
	 * @param string $cur_value The current value of the input field. For a simple HTML input
	 *  element this should end up in the value attribute.
	 * @param string $input_name The name of the input. For a simple HTML input element this should
	 *  end up in the name attribute.
	 * @param bool $disabled Is this input disabled?
	 * @param array $other_args An associative array of other parameters that were present in the
	 *  input definition.
	 */
	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );

		$this->addJsInitFunctionData(
			'bs_smwc_pf_mw_visualeditor_init',
			$this->getInitParams()
		);
		$this->addJsValidationFunctionData(
			'bs_smwc_pf_mw_visualeditor_validate',
			$this->getInitParams()
		);
	}

	/**
	 *
	 * @return string
	 */
	public static function getName(): string {
		return 'bs-mwvisualeditor';
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlText(): string {
		$html = Html::openElement(
			'div',
			[
				'id' => 'input_' . $this->mInputNumber . '_cnt',
				'class' => 'bs-mwvisualeditor-field-container',
			]
			+
			$this->makeDataAttributes()
		);
		$html .= Html::input(
			$this->mInputName,
			$this->mCurrentValue,
			'hidden',
			[
				'id' => 'input_' . $this->mInputNumber
			]
		);
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getResourceModuleNames() {
		$modules = parent::getResourceModuleNames();
		$modules[] = 'ext.BSSMWConnector.PF.Inputs.MWVisualEditor';

		return $modules;
	}

	/**
	 *
	 * @return array
	 */
	public static function getParameters() {
		$params = parent::getParameters();
		return $params;
	}

	/**
	 *
	 * @return array
	 */
	protected function makeDataAttributes() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	protected function getInitParams() {
		$params = [
			'input_name' => $this->mInputName,
			'current_value' => $this->mCurrentValue
		];
		return $params;
	}
}
