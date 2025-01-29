<?php

namespace BlueSpice\SMWConnector\AsyncAskHandler;

use BlueSpice\SMWConnector\Hook\ParserFirstCallInit\AsyncAsk;
use BlueSpice\SMWConnector\IAsyncAskHandler;
use MediaWiki\Html\Html;
use MediaWiki\Json\FormatJson;
use MediaWiki\Message\Message;

class Tree implements IAsyncAskHandler {

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
			'rootNode' => $renderer->getParam( 'rootNode', false ),
			'storeAction' => $this->getStoreAction(),
		] );

		return Html::rawElement( 'div', [
			'class' => 'bs-smw-connector-async-ask-tree-container',
			'data-query' => FormatJson::encode( $data )
		], Html::element( 'span', [
				'class' => 'loading-text',
			], Message::newFromKey( 'bs-extjs-loading' )->text() )
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules() {
		return [ 'ext.BSSMWConnector.async.tree' ];
	}

	/**
	 * @return string
	 */
	protected function getStoreAction() {
		return 'bs-smw-connector-tree-ask-store';
	}
}
