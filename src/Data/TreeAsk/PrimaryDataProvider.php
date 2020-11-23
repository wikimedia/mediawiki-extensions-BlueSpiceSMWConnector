<?php

namespace BlueSpice\SMWConnector\Data\TreeAsk;

use BlueSpice\SMWConnector\Data\Ask\PrimaryDataProvider as Base;
use BlueSpice\SMWConnector\Data\Ask\ReaderParams;
use BlueSpice\SMWConnector\Data\Ask\Schema;

class PrimaryDataProvider extends Base {

	/**
	 * @param ReaderParams $params
	 * @param string $query
	 * @return string|string[]
	 */
	protected function decorateQueryString( ReaderParams $params, $query ) {
		$query = $params->getQuery();

		return str_replace( "{{{node}}}", $this->getNodeForQuery( $params ), $query );
	}

	/**
	 * @param ReaderParams $params
	 * @return string
	 */
	protected function getNodeForQuery( ReaderParams $params ) {
		$node = $params->getNode();
		$nodeBits = explode( '/', $node );

		return str_replace( '+', '/', array_pop( $nodeBits ) );
	}

	/**
	 * @inheritDoc
	 */
	protected function appendDataToRecord( $data, Schema $schema, ReaderParams $params ) {
		$data['id'] = $params->getNode() . '/' . str_replace( '/', '+', $data['page'] );

		return parent::appendDataToRecord( $data, $schema, $params );
	}
}
