<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

class Grid extends \PFFormInput {

	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );

		$this->addJsInitFunctionData( 'bs_smwc_pf_input_grid_init', $this->getJsInitFunctionData() );
	}

	public static function getName() {
		return 'bs-grid';
	}

	public function getHtmlText() {
		$html = \Html::openElement(
			'div',
			[
				'id' => 'input_' . $this->mInputNumber . '_cnt',
				'class' => 'bs-grid-field-container',
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
		return [
			'ext.BSSMWConnector.PF.Input.Grid'
		];
	}

	public static function getParameters() {
		$params = parent::getParameters();
		$params['colDef'] = array(
			'name' => 'colDef',
			'type' => 'string',
			'description' => wfMessage( 'bs-bssmwconnector-pf-forminput-grid-coldef' )->text()
		);
		$params['template'] = array(
			'name' => 'template',
			'type' => 'string',
			'description' => wfMessage( 'bs-bssmwconnector-pf-forminput-grid-template' )->text()
		);
		return $params;
	}

	protected function makeDataAttributes() {
		$colDefSource = $this->mOtherArgs['colDef'];
		$wikiPage = \WikiPage::factory( \Title::newFromText( $colDefSource ) );
		$content = $wikiPage->getContent();
		$colDef = \FormatJson::decode( $content->getNativeData() );

		$dataAttribs = [
			'data-template' => $this->mOtherArgs['template'],
			'data-coldef' => \FormatJson::encode( $colDef )
		];

		return $dataAttribs;
	}

}