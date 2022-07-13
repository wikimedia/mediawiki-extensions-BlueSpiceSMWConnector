<?php

namespace BlueSpice\SMWConnector\PageForms\Input;

/**
 * This class is just for b/c, so that `input type=bsvisualeditor` in `{{{field}}}`-definitions
 * still work. We override all `get*Types*`-methods so this field is not shown on
 * `Special:CreateForm` anymore. For new forms we want to have `input type=bs-mwvisualeditor` from
 * `BlueSpice\SMWConnector\PageForms\Input\MWVisualEditor` only.
 */
class LegacyBSSFVisualEditor extends MWVisualEditor {

	/**
	 *
	 * @return string
	 */
	public static function getName(): string {
		return 'bsvisualeditor';
	}

	/**
	 *
	 * @return array
	 */
	public static function getDefaultCargoTypes() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getDefaultCargoTypeLists() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getOtherCargoTypesHandled() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getOtherCargoTypeListsHandled() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getDefaultPropTypes() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getDefaultPropTypeLists() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getOtherPropTypesHandled() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getOtherPropTypeListsHandled() {
		return [];
	}
}
