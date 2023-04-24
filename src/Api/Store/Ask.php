<?php
namespace BlueSpice\SMWConnector\Api\Store;

use BlueSpice\Api\Store;
use BlueSpice\SMWConnector\Data\Ask\ReaderParams;
use BlueSpice\SMWConnector\Data\Ask\Store as AskStore;
use Wikimedia\ParamValidator\ParamValidator;

class Ask extends Store {
	/** @var string */
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
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_DEFAULT => '{}',
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
