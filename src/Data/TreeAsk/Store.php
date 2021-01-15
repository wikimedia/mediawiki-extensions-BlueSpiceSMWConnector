<?php

namespace BlueSpice\SMWConnector\Data\TreeAsk;

use BlueSpice\SMWConnector\Data\Ask\Store as Base;

class Store extends Base {
	/**
	 * @inheritDoc
	 */
	public function getReader() {
		return new Reader();
	}
}
