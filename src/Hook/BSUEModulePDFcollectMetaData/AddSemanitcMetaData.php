<?php

namespace BlueSpice\SMWConnector\Hook\BSUEModulePDFcollectMetaData;

use MediaWiki\Title\Title;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\Services\ServicesFactory;
use SMWDITime;

class AddSemanitcMetaData extends \BlueSpice\UEModulePDF\Hook\BSUEModulePDFcollectMetaData {
	/**
	 *
	 * @var array
	 */
	protected $requestedProperties = [];

	/**
	 *
	 * @var array
	 */
	protected $requestedPropertyValues = [];

	/**
	 * Output Format for DateTime of SMWDITime in $date of getPropertyValuesForTitle
	 * @var string
	 */
	public static $dateFormat = "d.m.Y";

	public function doProcess() {
		$this->requestedProperties = $this->getConfig()->get( 'UEModulePDFsmwProperties' );

		$this->getPropertyValuesForTitle( $this->title, $this->requestedPropertyValues );
		$this->addPropertyValuesToMeta( $this->meta );

		return true;
	}

	/**
	 *
	 * @param array &$meta
	 */
	protected function addPropertyValuesToMeta( &$meta ) {
		$meta = array_merge( $meta, $this->requestedPropertyValues );
	}

	/**
	 *
	 * @param Title|null $title
	 */
	protected function getPropertyValuesForTitle( Title $title = null ) {
		if ( !empty( $title ) ) {

			$subject = DataValueFactory::getInstance()->newDataValueByType( '_wpg', $title->getFullText() );
			$store = ServicesFactory::getInstance()->getStore();

			$pagesSemanticData = $store->getSemanticData( $subject->getDataItem() );

			$propertyValues = [];

			foreach ( $this->requestedProperties as $propertyName => $callback ) {
				$property = DIProperty::newFromUserLabel( ucfirst( $propertyName ) );
				$propertyValues = $pagesSemanticData->getPropertyValues( $property );

				foreach ( $propertyValues as $propertyValue ) {
					$value = $propertyValue->getSerialization();

					if ( $propertyValue instanceof SMWDITime ) {
						$date = $propertyValue->getMwTimestamp();
						$value = date( self::$dateFormat, $date );

					}

					if ( is_callable( $callback ) ) {
						$value = call_user_func_array( $callback, [ $propertyValue ] );
					}

					$this->requestedPropertyValues[ $propertyName ] = $value;
				}
			}
		}
	}

}
