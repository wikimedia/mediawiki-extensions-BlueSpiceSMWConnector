<?php

namespace BlueSpice\SMWConnector\Data\Ask;

use BlueSpice\Data\Filter;
use BlueSpice\Data\Filter\Date;
use BlueSpice\Data\Filter\Numeric;
use BlueSpice\Data\IPrimaryDataProvider;
use BlueSpice\Data\Record;
use BlueSpice\SMWConnector\Query\Language\AnyDescription;
use BlueSpice\SMWConnector\Query\Language\StoreFilter\DateFilterDescription;
use BlueSpice\SMWConnector\Query\Language\StoreFilter\FilterDescription;
use BlueSpice\SMWConnector\Query\Language\StoreFilter\StringFilterDescription;
use SMW\ApplicationFactory;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\Query\Language\Conjunction;
use SMW\Query\Language\Disjunction;
use SMW\StoreFactory;
use SMWDataItem;
use SMWDIWikiPage;
use SMWQuery;
use SMWQueryProcessor;
use SMWQueryResult;

class PrimaryDataProvider implements IPrimaryDataProvider {
	/** @var Schema|null */
	protected $schema = null;
	/** @var bool */
	protected $justCount = false;
	/** @var int */
	protected $count = 0;

	/**
	 *
	 * @param ReaderParams $params
	 * @return Record[]
	 */
	public function makeData( $params ) {
		$data = [];
		$this->schema = new Schema( $params->getProps() );
		$queryParam = $this->decorateQueryString( $params, $params->getQuery() );

		$this->justCount = true;
		$queryResult = $this->getQueryResult( $queryParam, $params );
		$this->count = $queryResult->getCountValue();
		if ( $this->count === 0 ) {
			return [];
		}
		$this->justCount = false;

		$queryResult = $this->getQueryResult( $queryParam, $params );
		if ( empty( $queryResult->getResults() ) ) {
			// Sanity
			return [];
		}

		foreach ( $queryResult->getResults() as $wikiPage ) {
			$data[] = $this->getRow( $wikiPage, $this->schema, $params );
		}

		return $data;
	}

	/**
	 * @return int
	 */
	public function getCount() {
		return $this->count;
	}

	/**
	 * @param string $query
	 * @param ReaderParams $params
	 * @return array
	 */
	private function getParamArray( $query, $params ) {
		$limit = $params->getLimit();

		$res = [ $query, "limit={$limit}" ];
		if ( !$this->justCount ) {
			$offset = $params->getStart() - 1;
			$res[] = "offset={$offset}";
		}

		$bsSortItems = $params->getSort();
		if ( !empty( $bsSortItems ) ) {
			$smwSort = [];
			$smwOrder = [];
			foreach ( $bsSortItems as $bsSortItem ) {
				$smwSort[] = $bsSortItem->getProperty();
				$smwOrder[] = $this->convertSortDirection( $bsSortItem->getDirection() );
			}

			$res[] = 'sort=' . implode( ',', $smwSort );
			$res[] = 'order=' . implode( ',', $smwOrder );
		}

		return $res;
	}

	/**
	 * Do prelimirary modification of query string
	 *
	 * @param ReaderParams $params
	 * @param string $query
	 * @return string
	 */
	protected function decorateQueryString( ReaderParams $params, $query ) {
		return $query;
	}

	/**
	 * @param ReaderParams $params
	 * @return array
	 */
	protected function getFilterDescriptions( ReaderParams $params ) {
		$filters = [];
		foreach ( $params->getFilter() as $filter ) {
			if ( $filter->getField() === '_global' ) {
				$globalDisjunction = $this->getGlobalDisjunction( $filter );
				if ( $globalDisjunction ) {
					$filters[] = $globalDisjunction;
				}
			} else {
				if ( $filter instanceof Date ) {
					$filters[] = new DateFilterDescription( $filter );
				} elseif ( $filter instanceof Numeric ) {
					$filters[] = new FilterDescription( $filter );
				} else {
					$filters[] = new StringFilterDescription( $filter );
				}
			}

			$filter->setApplied( true );
		}

		return $filters;
	}

	/**
	 * @param Filter $filter
	 * @return Disjunction|null
	 */
	private function getGlobalDisjunction( Filter $filter ) {
		$descriptions = [];
		// Always "ct" comparison
		foreach ( (array)$this->schema as $key => $def ) {
			if ( !isset( $def[Schema::PROPERTY_TYPE] ) ) {
				continue;
			}
			$type = $def[Schema::PROPERTY_TYPE];

			if ( !in_array( $type, [ '_txt', '_wpg', '_ema', '_tel', '_str' ] ) ) {
				continue;
			}
			$descriptions[] = new AnyDescription(
				$def[Schema::PROPERTY_NAME], $filter->getValue()
			);
		}

		return new Disjunction( $descriptions );
	}

	/**
	 * @param string $queryParam
	 * @param ReaderParams $params
	 * @return SMWQueryResult
	 */
	private function getQueryResult( $queryParam, $params ) {
		// This does a roundabout way of getting the query
		// 1. gets query from passed query string
		// 2. adds filters and global filter and creates a new query
		// This is needed because just adding descriptions to a query that
		// is created in this way does not work. Old QA is always respected
		$baseQuery = $this->getQuery( $queryParam, $params );

		$newDescriptions = array_merge( [
			$baseQuery->getDescription(),
		], $this->getFilterDescriptions( $params ) );

		$description = new Conjunction( $newDescriptions );
		$baseQuery->setDescription( $description );
		$query = $this->getQuery( $queryParam, $params, $baseQuery->getQueryString() );

		return ApplicationFactory::getInstance()->getStore()->getQueryResult( $query );
	}

	/**
	 * @param string $queryParam
	 * @param ReaderParams $readerParams
	 * @param string|null $queryString
	 * @return SMWQuery
	 */
	private function getQuery( $queryParam, $readerParams, $queryString = '' ) {
		[ $qs, $parameters, $printouts ] =
			SMWQueryProcessor::getComponentsFromFunctionParams(
				$this->getParamArray( $queryParam, $readerParams ), false
			);

		if ( $queryString !== '' ) {
			$qs = $queryString;
		}

		$query = SMWQueryProcessor::createQuery(
			$qs,
			SMWQueryProcessor::getProcessedParams( $parameters, $printouts ),
			SMWQueryProcessor::SPECIAL_PAGE,
			$this->justCount ? 'count' : '',
			$printouts
		);
		$query->setOption( SMWQuery::PROC_CONTEXT, 'API' );

		return $query;
	}

	/**
	 * @param SMWDIWikiPage $wikiPage
	 * @param Schema $schema
	 * @param ReaderParams $params
	 * @return Record
	 */
	private function getRow( SMWDIWikiPage $wikiPage, Schema $schema, ReaderParams $params ) {
		$recordData = [
			'page' => $wikiPage->getTitle()->getPrefixedText()
		];

		$store = StoreFactory::getStore();
		foreach ( (array)$schema as $field => $def ) {
			if ( !isset( $def[Schema::PROPERTY_NAME] ) ) {
				continue;
			}

			$values = $store->getPropertyValues(
				$wikiPage, DIProperty::newFromUserLabel( $def[Schema::PROPERTY_NAME ] )
			);
			$parsed = [];
			/** @var SMWDataItem $value */
			foreach ( $values as $value ) {
				$parsed[] = $this->convertValueForOutput( $value, $def );
			}
			// TODO: How to deal with multiple values? The field type does not match if its an array
			if ( count( $parsed ) === 1 ) {
				$parsed = $parsed[0];
			}

			$recordData[$field] = $parsed;
		}

		return $this->appendDataToRecord( $recordData, $schema, $params );
	}

	/**
	 * @param array $data
	 * @param Schema $schema
	 * @param ReaderParams $params
	 * @return Record
	 */
	protected function appendDataToRecord( $data, Schema $schema, ReaderParams $params ) {
		return new Record( (object)$data );
	}

	/**
	 * Get the correct value type for DI
	 *
	 * @param SMWDataItem $value
	 * @param array $def
	 * @return mixed
	 */
	protected function convertValueForOutput( $value, $def ) {
		$dataValue = DataValueFactory::getInstance()->newDataValueByItem( $value );
		if ( $def[Schema::PROPERTY_FORMAT ] ) {
			$dataValue->setOutputFormat( $def[Schema::PROPERTY_FORMAT] );
		}
		return $dataValue->getShortWikiText();
	}

	/**
	 * Covert to natural sorting
	 *
	 * @param string $direction
	 * @return string
	 */
	private function convertSortDirection( $direction ) {
		$direction = strtolower( $direction );
		if ( $direction === 'asc' ) {
			return 'n-asc';
		}
		if ( $direction === 'desc' ) {
			return 'n-desc';
		}

		return $direction;
	}
}
