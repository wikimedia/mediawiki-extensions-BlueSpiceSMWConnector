<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

class UserTags extends \PFFormInput {

	/**
	 *
	 * @var \User[]
	 */
	protected $users = [];

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

		$this->parseCurrentValue();

		if ( isset( $other_args['group'] ) ) {
			$this->setGroups();
		}

		$this->addJsInitFunctionData( 'bs_smwc_pf_input_usertags_init', $this->getInitParams() );
	}

	/**
	 *
	 * @return string
	 */
	public static function getName() {
		return 'bs-usertags';
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlText() {
		$html = \Html::openElement(
			'span',
			[
				'id' => 'input_' . $this->mInputNumber . '_cnt',
				'class' => 'inputSpan',
				'style' => 'display:inline-block;'
			]
		);
		$users = implode( ',', $this->users );
		$html .= \Html::input(
			$this->mInputName,
			"$users",
			'hidden',
			[
				'id' => 'input_' . $this->mInputNumber
			]
		);
		$html .= \Html::closeElement( 'span' );

		return $html;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getResourceModuleNames() {
		return [
			'ext.BSSMWConnector.PF.Input.UserTags'
		];
	}

	/**
	 * If value is name of user page ( User: FooBar ), parse it
	 * and set username as current value
	 */
	protected function parseCurrentValue() {
		$this->mCurrentValue = explode( ',', $this->mCurrentValue );
		foreach ( $this->mCurrentValue as $userName ) {
			$user = \User::newFromName( $userName );
			if ( !$user ) {
				continue;
			}
			$this->users[] = $user;
		}
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
