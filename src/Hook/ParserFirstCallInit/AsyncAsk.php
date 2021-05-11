<?php

namespace BlueSpice\SMWConnector\Hook\ParserFirstCallInit;

use BlueSpice\ExtensionAttributeBasedRegistry;
use BlueSpice\Hook\ParserFirstCallInit;
use BlueSpice\SMWConnector\IAsyncAskHandler;
use Html;
use MWException;
use Parser;

/**
 * Handler for #asyncAsk parser function
 */
class AsyncAsk extends ParserFirstCallInit {
	/** @var array */
	private $params = [];

	/**
	 * @return bool
	 * @throws MWException
	 */
	protected function doProcess() {
		$this->parser->setFunctionHook( 'asyncAsk', [ $this, 'render' ] );

		return true;
	}

	/**
	 * @param Parser $parser
	 * @return string
	 */
	public function render( Parser $parser ) {
		$params = func_get_args();
		array_shift( $params );

		$query = array_shift( $params );
		$this->params = $params;

		$format = $this->getParam( 'format', false, 'grid' );
		$formatHandler = $this->getFormatHandler( $format );
		if ( !$formatHandler instanceof IAsyncAskHandler ) {
			return Html::element( 'span', [
				'style' => 'color: red;',
			], $this->getContext()->msg(
				'bs-smw-connector-async-ask-unknown-handler'
			)->params( $format )->text() );
		}

		$parser->getOutput()->addModules( $formatHandler->getRLModules() );

		$data = [
			'query' => $query
		];
		return $formatHandler->getHtml( $this, $data );
	}

	/**
	 * Get array value of given param
	 *
	 * @param string $search
	 * @param bool|null $retAsArray
	 * @param mixed|null $default
	 * @return string|string[]|mixed
	 */
	public function getParam( $search, $retAsArray = true, $default = null ) {
		foreach ( $this->params as $param ) {
			if ( strpos( trim( $param ), "$search=" ) === 0 ) {
				$value = explode( ',', str_replace( "$search=", '', $param ) );
				array_walk( $value, static function ( $item ) {
					return trim( $item );
				} );

				if ( count( $value ) === 1 && !$retAsArray ) {
					return $value[0];
				}

				return $value;
			}
		}

		return $default;
	}

	/**
	 * Get ExtJS-ready props array
	 *
	 * @return array
	 */
	public function getProps() {
		$raw = $final = [];
		foreach ( $this->params as $param ) {
			if ( strpos( $param, '?' ) === 0 ) {
				$raw[] = substr( $param, 1 );
			}
		}

		foreach ( $raw as $prop ) {
			$bits = explode( '=', $prop );
			// Value will be a label or NULL
			$final[array_shift( $bits )] = array_shift( $bits );
		}

		return $final;
	}

	/**
	 * Get ExtJS-ready sort array
	 *
	 * @return array
	 */
	public function getSort() {
		$res = [];
		$sort = $this->getParam( 'sort' );
		$order = $this->getParam( 'order' );
		if ( !$sort ) {
			return [];
		}
		if ( !$order ) {
			$order = [ 'asc' ];
		}

		$order = array_filter( $order, static function ( $item ) {
			return in_array( strtolower( $item ), [ 'asc', 'desc' ] );
		} );

		if ( !$order ) {
			$order = [ 'asc' ];
		}
		if ( count( $sort ) > count( $order ) ) {
			$order = array_pad( $order, count( $sort ), end( $order ) );
		}

		foreach ( $sort as $idx => $sortProp ) {
			$res[] = [
				'property' => $sortProp,
				'direction' => strtoupper( $order[$idx] ),
			];
		}

		return $res;
	}

	/**
	 * @param string $format
	 * @return IAsyncAskHandler|null
	 */
	private function getFormatHandler( $format ) {
		$attribute = new ExtensionAttributeBasedRegistry(
			'BlueSpiceSMWConnectorAsyncAskHandlers'
		);

		$callable = $attribute->getValue( trim( strtolower( $format ) ), null );
		if ( !is_callable( $callable ) ) {
			return null;
		}

		return call_user_func( $callable );
	}
}
