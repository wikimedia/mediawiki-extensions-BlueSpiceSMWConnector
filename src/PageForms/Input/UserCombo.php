<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

class UserCombo extends \PFFormInput {
	protected $currentUser = null;
	protected $groups = [];

	public function __construct( $input_number, $cur_value, $input_name, $disabled, $other_args ) {
		parent::__construct( $input_number, $cur_value, $input_name, $disabled, $other_args );

		$this->parseCurrentValue();

		if ( isset( $other_args['group'] ) ) {			$this->setGroups();
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
			$currentUser ? $currentUser->getName() : '',
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
	protected function parseCurrentValue() {
		if ( !$user = User::newFromName( $this->mCurrentValue ) ) {
			return;
		}
		$this->currentUser = $user;
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

	protected function getInitParams() {
		$params = [
			'input_name' => $this->mInputName,
			'current_value' => $this->mCurrentValue
		];

		if ( !empty( $this->groups ) ) {
			$params['groups'] = $this->groups;
		}
		return $params;
	}
}
