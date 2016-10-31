<?php

class BSSFVisualEditor extends SFTextAreaInput {

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
			'ext.BSSMWConnector.SF.VisualEditorField'
		);*/
	}

	protected function getTextAreaAttributes() {
		$aParams = parent::getTextAreaAttributes();
		$aParams['class'] .= ' bs-visualeditor';
		return $aParams;
	}
}