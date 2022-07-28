<?php

namespace BlueSpice\SMWConnector;

use BlueSpice\ExtensionAttributeBasedRegistry;

class PropertyValueProvidersRegistration {

	public static function addExtensions() {
		if ( defined( 'MW_PHPUNIT_TEST' ) ) {
			return true;
		}

		if ( !isset( $GLOBALS['sespgLocalDefinitions'] ) ) {
			$GLOBALS['sespgLocalDefinitions'] = [];
		}

		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceSMWConnectorExtraPropertyRegistry'
		);
		$callbackKeys = $registry->getAllKeys();
		foreach ( $callbackKeys as $callbackKey ) {
			$callback = $registry->getValue( $callbackKey );

			/** @var IPropertyValueProvider[] $propertyValueProvider */
			$propertyValueProviders = call_user_func( $callback );

			foreach ( $propertyValueProviders as $propertyValueProvider ) {
				if ( $propertyValueProvider instanceof IPropertyValueProvider ) {
					$GLOBALS['sespgLocalDefinitions'] += self::makeSESPDefinition(
						$propertyValueProvider
					);
					$GLOBALS['sespgEnabledPropertyList'][] = $propertyValueProvider->getId();
				}
			}
		}
	}

	/**
	 *
	 * @param IPropertyValueProvider $propertyValueProvider
	 * @return array
	 */
	private static function makeSESPDefinition( $propertyValueProvider ) {
		$def = [];
		$internalId = $propertyValueProvider->getId();
		$externalId = "__$internalId";
		$def[$internalId] = [
			'id'    => $externalId,
			'type'  => PropertyValueTypeDictionary::$types[$propertyValueProvider->getType()],
			'alias' => $propertyValueProvider->getAliasMessageKey(),
			'label' => $propertyValueProvider->getLabel(),
			'desc'  => $propertyValueProvider->getDescriptionMessageKey(),
			'callback'  => [ $propertyValueProvider, 'addAnnotation' ]
		];

		return $def;
	}

}
