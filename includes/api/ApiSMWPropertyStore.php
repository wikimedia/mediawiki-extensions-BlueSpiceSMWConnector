<?php

class ApiSMWPropertyStore extends BSApiExtJSStoreBase {

	/**
	 *
	 * @param string $query
	 * @return \stdClass[]
	 */
	protected function makeData( $query = '' ) {
		$result = new stdClass();

		$properties = [];
		$dbr = $this->getDB();

		$res = $dbr->select(
			[ 'smw_object_ids' ],
			[ 'smw_title', 'smw_id' ],
			[
				"smw_namespace" => SMW_NS_PROPERTY,
				"smw_title" . $dbr->buildLike( $query, $dbr->anyString() )
			],
			__METHOD__
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
