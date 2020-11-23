<?php

namespace BlueSpice\SMWConnector;

use BlueSpice\SMWConnector\Hook\ParserFirstCallInit\AsyncAsk;

interface IAsyncAskHandler {
	/**
	 * Get HTML to output
	 * @param AsyncAsk $renderer
	 * @param array $data
	 * @return string
	 */
	public function getHtml( AsyncAsk $renderer, array $data );

	/**
	 * Get required RL modules to load
	 *
	 * @return string|array
	 */
	public function getRLModules();
}
