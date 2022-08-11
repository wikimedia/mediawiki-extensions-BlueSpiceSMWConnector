<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

use MediaWiki\MediaWikiServices;
use TextContent;

class Grid extends \PFFormInput {

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
			'bs_smwc_pf_input_grid_init',
			$this->getJsInitFunctionData()
		);
	}

	/**
	 * @return string
	 */
	public static function getName() {
		return 'bs-grid';
	}

	/**
	 * @return string
	 */
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

	/**
	 * @return string[]
	 */
	public function getResourceModuleNames() {
		return [
			'ext.BSSMWConnector.PF.Input.Grid'
		];
	}

	/**
	 * @return array
	 */
	public static function getParameters() {
		$params = parent::getParameters();
		$params['colDef'] = [
			'name' => 'colDef',
			'type' => 'string',
			'description' => wfMessage( 'bs-bssmwconnector-pf-forminput-grid-coldef' )->text()
		];
		$params['template'] = [
			'name' => 'template',
			'type' => 'string',
			'description' => wfMessage( 'bs-bssmwconnector-pf-forminput-grid-template' )->text()
		];
		return $params;
	}

	/**
	 * @return array
	 */
	protected function makeDataAttributes() {
		$colDefSource = $this->mOtherArgs['colDef'];
		$wikiPage = MediaWikiServices::getInstance()->getWikiPageFactory()
			->newFromTitle( \Title::newFromText( $colDefSource ) );
		$content = $wikiPage->getContent();
		$contentText = ( $content instanceof TextContent ) ? $content->getText() : '{}';
		$colDef = \FormatJson::decode( $contentText );

		$dataAttribs = [
			'data-template' => $this->mOtherArgs['template'],
			'data-coldef' => \FormatJson::encode( $colDef )
		];

		return $dataAttribs;
	}
}
