<?php

namespace BlueSpice\SMWConnector\Data\Ask;

use BlueSpice\Data\NullTrimmer;
use BlueSpice\Data\Reader as BaseReader;
use BlueSpice\Data\ResultSet;
use MWException;
use RuntimeException;

class Reader extends BaseReader {
	/** @var ReaderParams|null  */
	protected $params = null;
	/** @var Schema|null  */
	protected $schema = null;
	/** @var PrimaryDataProvider */
	protected $primaryProvider = null;

	/**
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		if ( !$this->primaryProvider ) {
			$primaryProviderClass = $this->getPrimaryDataProviderClass();
			if ( !class_exists( $primaryProviderClass ) ) {
				throw new MWException(
					'PrimaryDataProvider class ' . $primaryProviderClass . ' does not exist'
				);
			}
			$this->primaryProvider = new $primaryProviderClass();
		}
		return $this->primaryProvider;
	}

	/**
	 * @return Schema
	 * @throws RuntimeException
	 */
	public function getSchema() {
		if ( $this->schema === null ) {
			if ( !$this->params instanceof ReaderParams ) {
				throw new RuntimeException(
					__METHOD__ . ' must be an instance of ' . ReaderParams::class
				);
			}
			$this->schema = new Schema( $this->params->getProps() );
		}

		return $this->schema;
	}

	/**
	 * @inheritDoc
	 */
	protected function makeTrimmer( $params ) {
		return new NullTrimmer();
	}

	/**
	 * @return SecondaryDataProvider
	 */
	public function makeSecondaryDataProvider() {
		return new SecondaryDataProvider( $this->getSchema() );
	}

	/**
	 * @inheritDoc
	 */
	public function read( $params ) {
		$this->params = $params;
		$parentRS = parent::read( $params );
		$primaryProvider = $this->primaryProvider;

		return new ResultSet( $parentRS->getRecords(), $primaryProvider->getCount() );
	}

	/**
	 * @return string
	 */
	protected function getPrimaryDataProviderClass() {
		return PrimaryDataProvider::class;
	}

}
