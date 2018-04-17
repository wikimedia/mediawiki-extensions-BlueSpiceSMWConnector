<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

class UserCombo extends \PFFormInput {
	protected $currentUser;
	protected $groups = [];

	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );

		$this->parseCurrentValue();
		//$this->setUser();

		if( isset( $other_args['group'] ) ) {
			$this->setGroups();
		}

		$this->addJsInitFunctionData( 'bs.smwc.pf.input.usercombo.init', $this->getInitParams() );
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
			'',
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

	/**
	 * If value is name of user page ( User: FooBar ), parse it
	 * and set username as current value
	 */
	public function parseCurrentValue() {
		if( strpos( $this->mCurrentValue, ':' ) !== false ) {
			$userPage = \Title::newFromText( $this->mCurrentValue );
			if( $userPage->getNamespace() == NS_USER ) {
				$this->mCurrentValue = $userPage->getText();
			}
		}
	}

	/**
	 * Currently not used, sets User object from username
	 */
	public function setUser() {
		if( $this->mCurrentValue == '' ) {
			return null;
		}

		$user = \User::newFromName( $this->mCurrentValue );
		if( $user instanceof \User && $user->getId() > 0 ) {
			$this->currentUser = $user;
		}
	}

	/**
	 * Parses "group" parameter to an array
	 */
	public function setGroups() {
		$raw = $this->mOtherArgs['group'];
		$groups = explode( ',', $raw );
		foreach( $groups as &$group ) {
			$group = trim( $group );
			$group = strtolower( $group );
		}
		$this->groups = $groups;
	}

	protected function getInitParams() {
		$params = [
			'input_name' => $this->mInputName,
			'current_value' => $this->mCurrentValue
		];

		if( !empty( $this->groups ) ) {
			$params['groups'] = $this->groups;
		}
		return $params;
	}
}