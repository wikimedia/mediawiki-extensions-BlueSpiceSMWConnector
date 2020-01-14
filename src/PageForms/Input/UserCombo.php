<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

use Html;
use User;

class UserCombo extends \PFFormInput {
	/**
	 *
	 * @var array
	 */
	protected $groups = [];

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
		if ( isset( $other_args['group'] ) ) {
			$this->setGroups();
		}

		$this->addJsInitFunctionData( 'bs_smwc_pf_input_usercombo_init', $this->getInitParams() );
	}

	/**
	 *
	 * @return string
	 */
	public static function getName() {
		return 'bs-usercombo';
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlText() {
		$html = Html::openElement(
			'span',
			[
				'id' => 'input_' . $this->mInputNumber . '_cnt',
				'class' => 'inputSpan',
				'style' => 'display:inline-block;'
			]
		);
		$html .= Html::input(
			$this->mInputName,
			$this->mCurrentValue,
			'hidden',
			[
				'id' => 'input_' . $this->mInputNumber
			]
		);
		$html .= Html::closeElement( 'span' );

		return $html;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getResourceModuleNames() {
		return [
			'ext.BSSMWConnector.PF.Input.UserCombo'
		];
	}

	/**
	 *
	 * @return User|null
	 */
	protected function getUser() {
		if ( !$this->mCurrentValue ) {
			return null;
		}
		$username = array_pop( explode( ':', $this->mCurrentValue ) );
		$user = User::newFromName( $username );
		if ( !$user ) {
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
		foreach ( $groups as &$group ) {
			$group = trim( $group );
			$group = strtolower( $group );
		}
		$this->groups = $groups;
	}

	/**
	 *
	 * @return array
	 */
	protected function getInitParams() {
		$user = $this->getUser();
		$params = [
			'input_name' => $this->mInputName,
			'current_value' => $this->mCurrentValue
		];

		if ( $user instanceof User ) {
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

		if ( !empty( $this->groups ) ) {
			$params['groups'] = $this->groups;
		}

		return $params;
	}
}
