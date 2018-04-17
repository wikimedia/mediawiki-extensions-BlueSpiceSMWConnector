<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

class UserTags extends \PFFormInput {

	public static function getName() {
		return 'bs-usertags';
	}

	public function getHtmlText() {
		return static::class;
	}

	public function getResourceModuleNames() {
		return [
			'ext.BSSMWConnector.PF.Input.UserTags'
		];
	}
}