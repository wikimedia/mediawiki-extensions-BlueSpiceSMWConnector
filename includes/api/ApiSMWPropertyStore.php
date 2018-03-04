<?php

class ApiSMWPropertyStore extends BSApiExtJSStoreBase {

	protected function makeData( $query = '' ) {
		$result = new stdClass();

		$properties = array();
		$dbr = $this->getDB();

		$res = $dbr->select(
			array( 'smw_object_ids' ),
			array( 'smw_title', 'smw_id' ),
			array(
				"smw_namespace" => SMW_NS_PROPERTY,
				"smw_title" . $dbr->buildLike( $query, $dbr->anyString() )
			)
		);

		foreach ( $res as $row ) {
			$propertyData = new stdClass();

			$propertyData->prop_id = (int)$row->smw_id;
			$propertyData->prop_title = $row->smw_title;

			$properties[ $row->smw_title ] = $propertyData;
		}
		ksort( $properties );
		$properties = array_values( $properties );

		return $properties;
	}
}