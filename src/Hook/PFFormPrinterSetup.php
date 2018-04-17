<?php

namespace BlueSpice\SMWConnector\Hook;

//TODO: when on 'master', derive from BlueSpice\Hook and remove redundant code
abstract class PFFormPrinterSetup {

	/**
	 *
	 * @var \PFFormPrinter
	 */
	protected $formPrinter = null;

	/**
	 *
	 * @param null $context As long as not on 'master'
	 * @param null $config As long as not on 'master'
	 * @param \PFFormPrinter $formPrinter
	 */
	public function __construct( $context, $config, $formPrinter ) {
		$this->formPrinter = $formPrinter;
	}

	/**
	 *
	 * @param \PFFormPrinter $formPrinter
	 */
	public static function callback( $formPrinter ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$formPrinter
		);
		return $hookHandler->process();
	}

	public function process() {
		$result = $this->doProcess();
		return $result;
	}

	protected abstract function doProcess();
}
