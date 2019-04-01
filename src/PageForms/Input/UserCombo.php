<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

class UserCombo extends \PFFormInput {
	protected $groups = [];

	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );
		if( isset( $other_args['group'] ) ) {
			$this->setGroups();
		}

		$this->addJsInitFunctionData( 'bs_smwc_pf_input_usercombo_init', $this->getInitParams() );
	}

	public static function getName() {
		return 'bs-usercombo';
	}

	public function getHtmlText() {
		$html = \Html::openElement(
			'span',
			[
				'id' => 'input_' . $this->mInputNumber . '_cnt',
				'class' => 'inputSpan',
				'style' => 'display:inline-block;'
			]
		);
		$html .= \Html::input(
			$this->mInputName,
			$this->mCurrentValue,
			'hidden',
			[
				'id' => 'input_' . $this->mInputNumber
			]
		);
		$html .= \Html::closeElement( 'span' );

		return $html;
	}

	public function getResourceModuleNames() {
		return [
			'ext.BSSMWConnector.PF.Input.UserCombo'
		];
	}


	protected function getUser() {
		if ( !$this->mCurrentValue ) {
			return null;
		}
		$username = array_pop( explode( ':', $this->mCurrentValue ) );
		if( !$user = \User::newFromName( $username ) ) {
			return null;
		}
		return $user;
	}

	/**
	 * Parses "group" parameter to an array
	 */
	protected function setGroups() {
		$raw = $this->mOtherArgs['group'];
		$groups = explode( ',', $raw );
		foreach( $groups as &$group ) {
			$group = trim( $group );
			$group = strtolower( $group );
		}
		$this->groups = $groups;
	}

	protected function getInitParams() {
		$user = $this->getUser();
		$params = [
			'input_name' => $this->mInputName,
			'current_value' => $this->mCurrentValue
		];

		if ( $user instanceof \User ) {
			$params['userRecord'] = [
				'user_id' => $user->getId(),
				'user_name' => $user->getName(),
				'user_real_name' => $user->getRealName(),
				'user_registration' => $user->getRegistration(),
				'user_editcount' => $user->getEditCount(),
				'groups' => $user->getEffectiveGroups(),
				'display_name' => $user->getRealName() ?: $user->getName(),
				'page_prefixed_text' => $user->getUserPage()->getPrefixedText()
			];
		}

		if( !empty( $this->groups ) ) {
			$params['groups'] = $this->groups;
		}

		return $params;
	}
}