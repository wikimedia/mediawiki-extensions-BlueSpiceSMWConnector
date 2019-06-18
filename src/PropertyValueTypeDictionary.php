<?php

namespace BlueSpice\SMWConnector;

use SMWDataItem;

class PropertyValueTypeDictionary {

	public static $types = [
		SMWDataItem::TYPE_BLOB => '_txt', // Text type
		SMWDataItem::TYPE_URI => '_uri', // URL/URI type
		SMWDataItem::TYPE_WIKIPAGE => '_wpg', // Page type
		SMWDataItem::TYPE_NUMBER => '_num', // Number type
		SMWDataItem::TYPE_TIME => '_dat', // Time type
		SMWDataItem::TYPE_BOOLEAN => '_boo', // Boolean type
		SMWDataItem::TYPE_CONTAINER => '_rec', // Value list type (replacing former nary properties)
		SMWDataItem::TYPE_GEO => '_geo', // Geographical coordinates
		SMWDataItem::TYPE_CONCEPT => '__con', // Special concept page type
		SMWDataItem::TYPE_PROPERTY => '__pro', // Property type
	];

}
