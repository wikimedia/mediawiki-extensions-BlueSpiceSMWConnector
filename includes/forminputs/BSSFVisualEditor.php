<?php



class BSSFVisualEditor extends PFTextAreaInput {
	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );

		$this->addJsInitFunctionData( 'bs.smwc.pf.input.visualeditorfield.init', $this->getInitParams() );
	}

	public static function getName() {
		return 'bsvisualeditor';
	}

	/**
	 * ATTENTION: It seems that SF does not include the module messages by
	 * default! See SMWConnectorHooks::onBeforePageDisplay
	 * @return array
	 */
	public function getResourceModuleNames() {
		$modules = parent::getResourceModuleNames();
		if ( $modules == null ) {
			$modules = [];
		}
		if ( is_string( $modules ) ) {
			$modules = [ $modules ];
		}
		$modules[] = 'ext.BSSMWConnector.SF.VisualEditorField';

		return $modules;
	}

	protected function getTextAreaAttributes() {
		$textarea_attrs = parent::getTextAreaAttributes();
		$textarea_attrs['class'] .= ' bs-visualeditor-field';
		return $textarea_attrs;
	}

	protected function getInitParams() {
		$params = [
			'input_name' => $this->mInputName,
			'current_value' => $this->mCurrentValue
		];
		return $params;
	}
}
