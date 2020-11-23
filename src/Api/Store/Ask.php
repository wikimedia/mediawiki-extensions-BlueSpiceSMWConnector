<?php
namespace BlueSpice\SMWConnector\Api\Store;

use BlueSpice\Api\Store;
use BlueSpice\SMWConnector\Data\Ask\ReaderParams;
use BlueSpice\SMWConnector\Data\Ask\Store as AskStore;

class Ask extends Store {
	/** @var string  */
	protected $metaData = 'metaData';

	/**
	 * @return AskStore
	 */
	protected function makeDataStore() {
		return new AskStore();
	}

	/**
	 * @inheritDoc
	 */
	public function getAllowedParams() {
		return parent::getAllowedParams() + [
			'props' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => '{}',
				static::PARAM_HELP_MSG => 'apihelp-bs-smw-connector-ask-store-props-param',
			]
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
		] );
	}
}
