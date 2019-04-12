<?php

class BSGridResultPrinter extends SMW\RawResultPrinter {
	public function getName() {
		return $this->msg( 'bs-bssmwconnector-srf-printername-bsgrid' )->text();
	}

	protected function getHtml( array $data ) {
		$this->isHTML = true;
		$id = $this->getId();
		$this->encode( $id, $data ); // Creates a client side JS variable accessible via mw.config.get($id)
		$this->addResources( 'ext.srf.bsextjsgrid' );

		return Html::element(
			'div',
			[
				'id' => $id,
				'class' => 'srf-bsextjsgrid',
				# 'data-external-class' => ( $this->params['class'] ? $this->params['class'] : '' )
			],
			''
		);
	}

	/**
	 * @see SMWResultPrinter::getParamDefinitions
	 * @param array $definitions array of IParamDefinition
	 *
	 * @return array of IParamDefinition|array
	 */
	public function getParamDefinitions( array $definitions ) {
		// Just to remember that one can define own params :)
		return parent::getParamDefinitions( $definitions );
	}
}
