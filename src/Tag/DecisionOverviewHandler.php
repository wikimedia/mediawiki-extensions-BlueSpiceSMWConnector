<?php

namespace BlueSpice\SMWConnector\Tag;

use BlueSpice\Tag\Handler;
use Html;
use Language;
use MediaWiki\Title\Title;
use Parser;
use PPFrame;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\Query\QueryResult;
use SMW\Services\ServicesFactory;
use SMW\Store;
use SMWQuery;
use SMWQueryProcessor;

class DecisionOverviewHandler extends Handler {

	/** @var Language */
	private $language = null;

	/**
	 * @param string $processedInput
	 * @param array $processedArgs
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param Language $language
	 */
	public function __construct( $processedInput, array $processedArgs, Parser $parser,
		PPFrame $frame, Language $language ) {
		parent::__construct( $processedInput, $processedArgs, $parser, $frame );
		$this->language = $language;
	}

	/**
	 *
	 * @return string
	 */
	public function handle() {
		$categories = $this->createSMWformat( $this->processedArgs[DecisionOverview::ATTR_CATEGORIES], 'categories' );
		$namespaces = $this->createSMWformat( $this->processedArgs[DecisionOverview::ATTR_NAMESPACES], 'namespaces' );
		$prefix = $this->createSMWformat( $this->processedArgs[DecisionOverview::ATTR_PREFIX], 'prefix' );
		$this->parser->getOutput()->addModuleStyles( 'ext.BSSMWConnector.decisionOverview.styles' );

		$query = '{{#ask:' . $categories . $namespaces . $prefix . '[[Decision::+]]|?Decision}}';
		$queryResult = $this->runSMWQuery( $query );
		$results = $queryResult->getResults();

		$store = ServicesFactory::getInstance()->getStore();

		$smwData = [];
		foreach ( $results as $DIWikiPage ) {
			$title = $DIWikiPage->getTitle();
			if ( $title === null || !$title->exists() ) {
				continue;
			}
			$smwData[] = $this->getProperties( $DIWikiPage, $store );
		}

		if ( empty( $smwData ) ) {
			return Html::element( 'div', [],
				wfMessage( 'bs-smwconnector-decision-overview-no-result' )->plain()
			);
		}
		$table = $this->getDecisionTable( $smwData );

		return $table;
	}

	/**
	 * @param array $decisions
	 * @return string
	 */
	private function getDecisionTable( array $decisions ): string {
		$html = Html::openElement( 'table',
			[
				'id' => 'decisionOverview',
				'class' => 'decisionOverview-table'
			] );

		$html .= $this->getTableHeader();

		foreach ( $decisions as $decision ) {
			$html .= $this->appendDecisionRow( $decision );
		}

		$html .= Html::closeElement( 'table' );
		return $html;
	}

	/**
	 *
	 * @return string
	 */
	private function getTableHeader(): string {
		$html = Html::openElement( 'thead', [
			'class' => 'decisionOverview-header'
		] );

		$html .= Html::openElement( 'tr', [
			'class' => 'decisionOverview-header-row'
		] );

		$html .= Html::element( 'th', [
			'class' => 'decisionOverview-header-cell'
		], wfMessage( 'bs-smwconnector-decision-overview-table-heading-page-label' )->plain() );

		$html .= Html::element( 'th', [
			'class' => 'decisionOverview-header-cell'
		], wfMessage( 'bs-smwconnector-decision-overview-table-heading-decisions-label' )->plain() );

		$html .= Html::closeElement( 'tr' );
		$html .= Html::closeElement( 'thead' );
		return $html;
	}

	/**
	 *
	 * @param array $decision
	 * @return string
	 */
	private function appendDecisionRow( $decision ): string {
		$html = Html::openElement( 'tr', [
			'class' => 'decisionOverview-row'
		] );

		$html .= $this->appendDecisionPage( $decision[ 'page' ] );
		$html .= $this->appendDecisionValue( $decision );

		$html .= Html::closeElement( 'tr' );
		return $html;
	}

	/**
	 *
	 * @param Title|null $pageTitle
	 * @return string
	 */
	private function appendDecisionPage( $pageTitle ): string {
		if ( !$pageTitle ) {
			return '';
		}
		$html = Html::openElement( 'td', [
			'class' => 'decisionOverview-cell'
		] );

		$html .= Html::element( 'a', [
			'href' => $pageTitle->getLocalURL()
		], $pageTitle->getFullText() );

		$html .= Html::closeElement( 'td' );

		return $html;
	}

	/**
	 *
	 * @param array $decision
	 * @return string
	 */
	private function appendDecisionValue( $decision ): string {
		$html = Html::openElement( 'td', [
			'class' => 'decisionOverview-cell'
		] );
		foreach ( $decision['decisions'] as $decisionValue ) {
			$html .= Html::element( 'div', [
				'class' => 'cd-decision-entry'
			], $decisionValue );
		}
		$html .= Html::closeElement( 'td' );

		return $html;
	}

	/**
	 * @param string $query
	 * @return QueryResult
	 */
	private function runSMWQuery( string $query ): QueryResult {
		[ $qs, $parameters, $printouts ] =
			SMWQueryProcessor::getComponentsFromFunctionParams(
				[ $query ], false
			);

		$query = SMWQueryProcessor::createQuery(
			$qs,
			SMWQueryProcessor::getProcessedParams( $parameters, $printouts ),
			SMWQueryProcessor::SPECIAL_PAGE,
			'',
			$printouts
		);
		$query->setOption( SMWQuery::PROC_CONTEXT, 'API' );

		return ServicesFactory::getInstance()->getStore()->getQueryResult( $query );
	}

	/**
	 * @param string $args
	 * @param string $format
	 * @return string
	 */
	private function createSMWformat( string $args, string $format ): string {
		if ( empty( $args ) ) {
			return '';
		}

		$argsArray = explode( '|', $args );
		$argsString = '';
		$multiple = false;
		switch ( $format ) {
			case 'categories':
				foreach ( $argsArray as $arg ) {
					if ( $multiple ) {
						$argsString .= 'OR';
					}
					$argsString .= "[[Category:$arg]]";
					$multiple = true;
				}
				break;
			case 'namespaces':
				foreach ( $argsArray as $arg ) {
					if ( $multiple ) {
						$argsString .= 'OR';
					}
					if ( $arg === 0 ) {
						$namespace = '';
					} else {
						$namespace = $this->language->getNsText( $arg );
					}
					$argsString .= "[[$namespace:+]]";
					$multiple = true;
				}
				break;
			case 'prefix':
				foreach ( $argsArray as $arg ) {
					$argsString .= "[[$arg]]";
				}
				break;
		}

		return $argsString;
	}

	/**
	 *
	 * @param DIWikiPage $DIWikiPage
	 * @param DIProperty $DIProperty
	 * @param array $printouts
	 * @param Store $store
	 * @return array
	 */
	public function getPropertyData(
		DIWikiPage $DIWikiPage, DIProperty $DIProperty, array $printouts, Store $store
	) {
		$property = $DIProperty->getCanonicalLabel();
		if ( !in_array( strtolower( $property ), $printouts ) ) {
			return [];
		}

		$DIValues = $store->getPropertyValues( $DIWikiPage, $DIProperty );
		$values = [];
		foreach ( $DIValues as $DIValue ) {
			$value = $DIValue->getSerialization();
			$hashPosition = strpos( $value, '#' );
			if ( $hashPosition ) {
				$value = substr( $value, 0, $hashPosition );
			}
			$values[] = str_replace( "_", " ", $value );
		}

		$decisions = [
			'page' => $DIWikiPage->getTitle(),
			'decisions' => $values
		];
		return $decisions;
	}

	/**
	 * @param DIWikiPage $DIWikiPage
	 * @param Store $store
	 * @return array
	 */
	public function getProperties( DIWikiPage $DIWikiPage, Store $store ): array {
		$printouts = [ 'decision' ];
		$propertiesData = [];
		$semanticData = $store->getSemanticData( $DIWikiPage );
		$DIProperties = $semanticData->getProperties();
		/** @var \SMW\DIProperty $standardProperty */
		foreach ( $DIProperties as $DIProperty ) {
			$values = $this->getPropertyData( $DIWikiPage, $DIProperty, $printouts, $store );
			if ( empty( $values ) ) {
				continue;
			}
			$propertiesData = array_merge( $propertiesData, $values );
		}
		return $propertiesData;
	}

}
