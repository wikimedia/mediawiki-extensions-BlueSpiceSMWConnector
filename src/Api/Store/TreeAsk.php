<?php
namespace BlueSpice\SMWConnector\Api\Store;

use BlueSpice\SMWConnector\Data\TreeAsk\ReaderParams;
use BlueSpice\SMWConnector\Data\TreeAsk\Store;

class TreeAsk extends Ask {

	/**
	 * @return Store
	 */
	protected function makeDataStore() {
		return new Store();
	}

	/**
	 * @inheritDoc
	 */
	public function getAllowedParams() {
		return parent::getAllowedParams() + [
			'node' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_HELP_MSG => 'apihelp-bs-smw-connector-ask-tree-store-node-param',
			],
		];
	}

	/**
	 *
	 * @return ReaderParams
	 */
	protected function getReaderParams() {
		return new ReaderParams( [
			'query' => $this->getParameter( 'query', null ),
			'start' => $this->getParameter( 'start', null ),
			'limit' => $this->getParameter( 'limit', null ),
			'filter' => $this->getParameter( 'filter', null ),
			'sort' => $this->getParameter( 'sort', null ),
			'props' => $this->getParameter( 'props' ),
			'node' => $this->getParameter( 'node' ),
		] );
	}
}
