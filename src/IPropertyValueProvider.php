<?php

namespace BlueSpice\SMWConnector;

use SESP\AppFactory;
use SMW\DIProperty;
use SMW\SemanticData;
use SMWDataItem;

// phpcs:ignore Generic.Files.LineLength.TooLong
// See https://github.com/SemanticMediaWiki/SemanticExtraSpecialProperties/blob/master/docs/extension.md

interface IPropertyValueProvider {

	/**
	 * @return string In form of "_MY_CUSTOM1"
	 */
	public function getId();

	/**
	 * One of SMWDataItem::TYPE_*
	 * @return int
	 */
	public function getType();

	/**
	 * @return string
	 */
	public function getAliasMessageKey();

	/**
	 * @return string
	 */
	public function getLabel();

	/**
	 * @return string
	 */
	public function getDescriptionMessageKey();

	/**
	 *
	 * @param AppFactory $appFactory
	 * @param DIProperty $property
	 * @param SemanticData $semanticData
	 * @return SMWDataItem
	 */
	public function addAnnotation( $appFactory, $property, $semanticData );
}
