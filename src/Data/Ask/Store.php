<?php

namespace BlueSpice\SMWConnector\Data\Ask;

use BlueSpice\Data\IStore;
use Exception;

class Store implements IStore {
	/**
	 * @inheritDoc
	 */
	public function getReader() {
		return new Reader();
	}

	/**
	 * @inheritDoc
	 */
	public function getWriter() {
		throw new Exception( 'This store does not support writing!' );
	}
}
