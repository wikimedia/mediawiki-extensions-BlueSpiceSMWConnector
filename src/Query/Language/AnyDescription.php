<?php

namespace BlueSpice\SMWConnector\Query\Language;

use SMW\Query\Language\Description;

class AnyDescription extends Description {
	/** @var string */
	protected $field;
	/** @var string */
	protected $value;

	/**
	 * @param string $field
	 * @param string $value
	 */
	public function __construct( $field, $value ) {
		$this->field = $field;
		$this->value = $value;
	}

	/**
	 * @inheritDoc
	 */
	public function getQueryString( $asValue = false ) {
		// The "page" column is the subject itself, not a property
		$prefix = $this->field === 'page' ? '' : $this->field . '::';
		$parts = [
			'[[' . $prefix . '~*' . $this->value . '*]]',
			'[[' . $prefix . '~' . $this->value . '*]]',
			'[[' . $prefix . '~*' . $this->value . ']]',
		];
		$string = implode( ' OR ', $parts );

		if ( $asValue ) {
			return "<q>$string</q>";
		}

		return $string;
	}

	/**
	 * @inheritDoc
	 */
	public function getFingerprint() {
		return 'A:' . md5( $this->field . '#' . $this->value );
	}

	/**
	 * @inheritDoc
	 */
	public function isSingleton() {
		return true;
	}
}
