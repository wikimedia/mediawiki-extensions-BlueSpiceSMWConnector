<?php

namespace BlueSpice\SMWConnector\AsyncAskHandler;

use BlueSpice\SMWConnector\Hook\ParserFirstCallInit\AsyncAsk;
use BlueSpice\SMWConnector\IAsyncAskHandler;
use FormatJson;
use Html;

class Grid implements IAsyncAskHandler {

	/**
	 * @return IAsyncAskHandler
	 */
	public static function factory() {
		return new static();
	}

	/**
	 * @inheritDoc
	 */
	public function getHtml( AsyncAsk $renderer, array $data ) {
		$data = array_merge( $data, [
			'props' => $renderer->getProps(),
			'sort' => $renderer->getSort(),
			'mainlabel' => $renderer->getParam( 'mainlabel', false ),
			'hiddenColumns' => $renderer->getParam( 'hiddenColumns' ),
			'storeAction' => $this->getStoreAction(),
		] );

		return Html::element( 'div', [
			'class' => 'bs-smw-connector-async-ask-grid-container',
			'data-query' => FormatJson::encode( $data )
		] );
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules() {
		return 'ext.BSSMWConnector.async.grid';
	}

	/**
	 * @return string
	 */
	protected function getStoreAction() {
		return 'bs-smw-connector-ask-store';
	}
}
