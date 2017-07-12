<?php



class BSSFVisualEditor extends PFTextAreaInput {

	public static function getName() {
		return 'bsvisualeditor';
	}

	/**
	 * ATTENTION: It seems that SF does not include the module messages by
	 * default! See SMWConnectorHooks::onBeforePageDisplay
	 * @return array
	 */
	public function getResourceModuleNames() {
		return parent::getResourceModuleNames();
		/*
		As we want VisualEditor to be available for standard input "free text"
		we need to load the whole module in 'BeforePageDisplay' hook
		*/
		/*return array(
			'ext.bluespice.visualEditor.styles',
			'ext.bluespice.visualEditor.tinymce',
			'ext.SMWConnector.SF.VisualEditorField'
		);*/
	}

	protected function getTextAreaAttributes() {
		$textarea_attrs = parent::getTextAreaAttributes();
		$textarea_attrs['class'] .= ' bs-visualeditor';
		return $textarea_attrs;
	}
}