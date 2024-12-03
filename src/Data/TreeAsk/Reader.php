<?php

namespace BlueSpice\SMWConnector\Data\TreeAsk;

use BlueSpice\SMWConnector\Data\Ask\Reader as Base;

class Reader extends Base {
	/**
	 * @inheritDoc
	 */
	protected function getPrimaryDataProviderClass() {
		return PrimaryDataProvider::class;
	}
}
