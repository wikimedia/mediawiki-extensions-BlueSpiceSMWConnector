<?php

class BSSMWCPageTemplates {

	/**
	 *
	 * @param PageTemplates $oSender
	 * @param BSPageTemplateList &$oPageTemplateList
	 * @param BSPageTemplateListRenderer &$oPageTemplateListRenderer
	 * @param Title $oTitle
	 * @return bool
	 */
	public static function onBSPageTemplatesBeforeRender( $oSender, &$oPageTemplateList, &$oPageTemplateListRenderer, $oTitle ) {
		$aData = $oPageTemplateList->getAll();
		foreach ( $aData as $iId => $aDataSet ) {
			if ( (int)$aDataSet['pt_template_namespace'] === PF_NS_FORM ) {
				$oFormTitle = Title::makeTitle(
					PF_NS_FORM,
					$aDataSet['pt_template_title'] . '/' . $oTitle->getPrefixedDBkey()
				);
				$oTargetTitle = SpecialPage::getTitleFor( 'FormEdit', $oFormTitle->getDBkey() );
				$aDataSet['target_url'] = $oTargetTitle->getLinkURL();
				$oPageTemplateList->set( $iId, $aDataSet );
			}
		}

		return true;
	}
}
