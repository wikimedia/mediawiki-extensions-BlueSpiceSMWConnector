<?php

namespace BlueSpice\SMWConnector\Data\Ask;

use BlueSpice\Data\FieldType;
use Message;
use SMW\DIProperty;

class Schema extends \BlueSpice\Data\Schema {
	public const PAGE = 'page';
	public const PAGE_LINK = 'page_link';
	public const PROPERTY_NAME = 'property_name';
	public const PROPERTY_FORMAT = 'property_format';
	public const PROPERTY_TYPE = 'property_type';
	public const LABEL = 'label';

	/**
	 * Schema constructor.
	 * @param array $props
	 */
	public function __construct( $props ) {
		$schemaDef = [
			static::PAGE => [
				static::FILTERABLE => true,
				static::SORTABLE => true,
				static::TYPE => FieldType::STRING,
				static::LABEL => Message::newFromKey(
					'bs-swm-connector-ask-store-page-label'
				)->text()
			],
			static::PAGE_LINK => [
				static::FILTERABLE => false,
				static::SORTABLE => false,
				static::TYPE => FieldType::STRING
			],
		];
		foreach ( $props as $prop => $label ) {
			$this->addPropToSchema( $prop, $label, $schemaDef );
		}

		parent::__construct( $schemaDef );
	}

	/**
	 * @param string $propName
	 * @param string $label
	 * @param array &$schemaDef
	 */
	private function addPropToSchema( $propName, $label, array &$schemaDef ) {
		$format = null;
		$propBits = explode( '#', $propName );
		$propName = array_shift( $propBits );
		if ( !empty( $propBits ) ) {
			$format = array_shift( $propBits );
		}
		$propName = explode( '#', $propName )[0];
		$prop = DIProperty::newFromUserLabel( $propName );
		$propType = $prop->findPropertyValueType();

		$label = str_replace( ' ', '_', $label ?? $prop->getLabel() );
		$schemaDef[$propName] = [
			static::FILTERABLE => true,
			static::SORTABLE => true,
			static::TYPE => $this->convertType( $propType ),
			static::PROPERTY_NAME => $propName,
			static::PROPERTY_FORMAT => $format,
			static::PROPERTY_TYPE => $propType,
			static::LABEL => $label,
		];

		// Add link field for each wikipage prop
		if ( $propType === '_wpg' ) {
			$schemaDef["{$label}_link"] = [
				static::FILTERABLE => false,
				static::SORTABLE => false,
				static::TYPE => FieldType::STRING,
			];
		}
	}

	/**
	 * @param string $type
	 * @return string
	 */
	private function convertType( $type ) {
		switch ( $type ) {
			case '_boo':
				return FieldType::BOOLEAN;
			case '_num':
			case '_qty':
				return FieldType::FLOAT;
			case '_dat':
				return FieldType::DATE;
			case '_txt':
				return FieldType::TEXT;
			default:
				return FieldType::STRING;
		}
	}

}
