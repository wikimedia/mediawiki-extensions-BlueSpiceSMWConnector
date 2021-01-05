<?php

namespace BlueSpice\SMWConnector;

use SMWDataItem;

class PropertyValueTypeDictionary {

	/** @var string[] */
	public static $types = [
		// Text type
		SMWDataItem::TYPE_BLOB => '_txt',
		// URL/URI type
		SMWDataItem::TYPE_URI => '_uri',
		// Page type
		SMWDataItem::TYPE_WIKIPAGE => '_wpg',
		// Number type
		SMWDataItem::TYPE_NUMBER => '_num',
		// Time type
		SMWDataItem::TYPE_TIME => '_dat',
		// Boolean type
		SMWDataItem::TYPE_BOOLEAN => '_boo',
		// Value list type (replacing former nary properties)
		SMWDataItem::TYPE_CONTAINER => '_rec',
		// Geographical coordinates
		SMWDataItem::TYPE_GEO => '_geo',
		// Special concept page type
		SMWDataItem::TYPE_CONCEPT => '__con',
		// Property type
		SMWDataItem::TYPE_PROPERTY => '__pro',
	];

}
