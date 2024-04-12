<?php

namespace BlueSpice\SMWConnector\Hook\BeforePageDisplay;

// TODO: when on 'master', derive from BlueSpice\Hook\BeforePageDisplay and remove redundant code
class AddModules {

	/**
	 *
	 * @var \OutputPage
	 */
	protected $out = null;
	/**
	 *
	 * @var \Skin
	 */
	protected $skin = null;

	/**
	 *
	 * @param null $context As long as not on 'master'
	 * @param null $config As long as not on 'master'
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 */
	public function __construct( $context, $config, $out, $skin ) {
		$this->out = $out;
		$this->skin = $skin;
	}

	/**
	 *
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 * @return bool
	 */
	public static function callback( $out, $skin ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$out,
			$skin
		);
		return $hookHandler->process();
	}

	public function process() {
		if ( $this->skipProcessing() ) {
			return true;
		}
		\Profiler::instance()->scopedProfileIn( "Hook " . __METHOD__ );
		$result = $this->doProcess();
		return $result;
	}

	protected function doProcess() {
		$this->out->addModules( 'ext.BSSMWConnector' );
		$this->out->addModuleStyles( 'ext.BSSMWConnector.styles' );
		return true;
	}

	/**
	 * Allow subclasses to define a skip condition
	 * @return bool
	 */
	protected function skipProcessing() {
		$title = $this->out->getTitle();
		if ( !$title ) {
			return true;
		}
		$onSpecialFormEdit = $title->isSpecial( 'FormEdit' );
		$inViewFormEdit =
			$this->out->getRequest()->getVal( 'action', 'view' ) === 'formedit';

		if ( $onSpecialFormEdit || $inViewFormEdit ) {
			return false;
		}
		return true;
	}
}
