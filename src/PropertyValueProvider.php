<?php

namespace BlueSpice\SMWConnector;

use SMWDataItem;

abstract class PropertyValueProvider implements IPropertyValueProvider {

	/**
	 *
	 * @return int
	 */
	public function getType() {
		return SMWDataItem::TYPE_WIKIPAGE;
	}

	/**
	 *
	 * @return IPropertyValueProvider[]
	 */
	public static function factory() {
		return [ new static() ];
	}
}
