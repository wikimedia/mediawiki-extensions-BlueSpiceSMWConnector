<?php
namespace BlueSpice\SMWConnector\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;

class AddFormEditAction extends SkinTemplateOutputPageBeforeExec {

	protected function doProcess() {
		$link = [
			'formedit' => [
				'position' => '1',
				'id' => 'ca-formedit',
				'text' => wfMessage( 'formedit' )->plain(),
				'title' => wfMessage( 'formedit' )->plain(),
				'href' => $this->skin->getRelevantTitle()->getLocalURL( 'action=formedit' )
			]
		];

		$this->template->data[SkinData::FEATURED_ACTIONS] += [ 'edit' =>  $link ];
	}

	protected function skipProcessing() {
		if ( empty( $this->template->data['content_navigation']['views']['formedit'] ) ) {
			return true;
		};
		return false;
	}
}
