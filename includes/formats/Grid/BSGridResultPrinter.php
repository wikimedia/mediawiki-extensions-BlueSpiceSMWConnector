<?php

use SMW\Query\QueryResult;
use SMW\Query\ResultPrinters\ResultPrinter;
use SRF\ResourceFormatter;

class BSGridResultPrinter extends ResultPrinter {

	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->msg( 'bs-bssmwconnector-srf-printername-bsgrid' )->text();
	}

	/**
	 * @see ResultPrinter::getResultText
	 *
	 * @param QueryResult $res
	 * @param int $outputmode
	 *
	 * @return string The output HTML
	 *
	 * {@inheritDoc}
	 */
	protected function getResultText( $res, $outputmode ) {
		$resourceFormatter = new ResourceFormatter();
		$data = $resourceFormatter->getData( $res, $outputmode, $this->params );

		$this->isHTML = true;
		$id = $resourceFormatter->session();

		// Encode data object
		$resourceFormatter->encode( $id, $data );

		// Init RL module
		$resourceFormatter->registerResources( [ 'ext.BSSMWConnector.resultGrid' ] );

		// Element includes info, spinner, and container placeholder
		return Html::rawElement(
			'div',
			[
				'id' => $id,
				'class' => 'srf-bssmwconnector-result-grid',
			]
		);
	}
}
