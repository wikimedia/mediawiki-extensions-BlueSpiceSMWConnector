<?php

namespace BlueSpice\SMWConnector\ExtendedSearch;

use BlueSpice\SMWConnector\ExtendedSearch\LookupModifier\AddSMWAggregation;
use BlueSpice\SMWConnector\ExtendedSearch\LookupModifier\AddSourceFields;
use BlueSpice\SMWConnector\ExtendedSearch\LookupModifier\ParseSMWFilters;
use BlueSpice\UtilityFactory;
use BS\ExtendedSearch\ILookupModifierProvider;
use BS\ExtendedSearch\ISearchDocumentProvider;
use BS\ExtendedSearch\ISearchSource;
use BS\ExtendedSearch\Lookup;
use BS\ExtendedSearch\Plugin\IDocumentDataModifier;
use BS\ExtendedSearch\Plugin\IFilterModifier;
use BS\ExtendedSearch\Plugin\IFormattingModifier;
use BS\ExtendedSearch\Plugin\IMappingModifier;
use BS\ExtendedSearch\Plugin\ISearchPlugin;
use BS\ExtendedSearch\SearchResult;
use BS\ExtendedSearch\Source\DocumentProvider\WikiPage as WikiPageProvider;
use BS\ExtendedSearch\Source\WikiPages;
use IContextSource;
use MediaWiki\MediaWikiServices;
use Message;
use RequestContext;
use Title;
use User;

class SMWData implements
	ISearchPlugin,
	IMappingModifier,
	IFormattingModifier,
	IDocumentDataModifier,
	IFilterModifier,
	ILookupModifierProvider
{

	/**
	 * @inheritDoc
	 */
	public function modifyMapping( ISearchSource $source, array &$indexSettings, array &$propertyMapping ): void {
		if ( !( $source instanceof WikiPages ) ) {
			return;
		}
		$propertyMapping['properties']['smwproperty'] = [
			'type' => 'nested',
			'dynamic' => false,
			'properties' => [
				'name' => [
					'type' => 'keyword'
				],
				'value' => [
					'type' => 'keyword',
				],
				'type' => [
					'type' => 'keyword'
				]
			]
		];
		$propertyMapping['properties']['smwproperty_agg'] = [
			'type' => 'keyword'
		];
		$propertyMapping['date_detection'] = false;
		$propertyMapping['dynamic'] = false;
	}

	/**
	 * @inheritDoc
	 */
	public function modifyDocumentData(
		ISearchDocumentProvider $documentProvider, array &$data, $uri, $documentProviderSource
	): void {
		if ( !( $documentProvider instanceof WikiPageProvider ) ) {
			return;
		}

		$this->getSemanticData( $documentProviderSource );

		$data['smwproperty'] = $this->semanticData;
		$data['smwproperty_agg'] = $this->getSemanticValueArray();
	}

	/**
	 * @inheritDoc
	 */
	public function modifyFilters(
		array &$aggregations, array &$filterCfg, array $fieldsWithANDEnabled, ISearchSource $source
	): void {
		if ( !( $source instanceof WikiPages ) ) {
			return;
		}
		if ( isset( $aggregations['smwproperty'] ) ) {
			$smwAgg = $aggregations['smwproperty'];
			unset( $aggregations['smwproperty'] );

			if ( $smwAgg['doc_count'] === 0 ) {
				return;
			}

			$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
			$filterConfig = $config->get( 'ESSMWPropertyFilter' );
			$filterType = isset( $filterConfig['type'] ) ? $filterConfig['type'] : 'blacklist';
			$filterValues = isset( $filterConfig['props'] ) ? $filterConfig['props'] : [];

			$smwAgg = $smwAgg['name'];
			foreach ( $smwAgg['buckets'] as $bucket ) {
				if ( !isset( $bucket['value']['buckets'][0] ) ) {
					// If bucket has no keys, skip it
					continue;
				}
				// First type of first key must be the type for all the keys
				$type = $bucket['value']['buckets'][0]['type']['buckets'][0]['key'];

				// We dont want to filer by dates, as range filters are not (yet) supported
				if ( $type == 'datetime' ) {
					continue;
				}

				foreach ( $bucket['value']['buckets'] as &$valueBucket ) {
					$valueBucket['type'] = $type;
					if ( $type === 'boolean' ) {
						$valueBucket['label'] = $this->getBooleanLabel( $valueBucket['key'] );
					}
				}
				// jQuery/Sizzle is very sensitive to what is an ID
				// of an element, encode value is always alphanumeric
				$key = base64_encode( 'smwproperty:' . $bucket['key'] );
				$key = rtrim( $key, '=' );

				if (
					$filterType === 'whitelist' &&
					!$this->inFilterValues( $bucket['key'], $filterValues )
				) {
					continue;
				}
				if (
					$filterType === 'blacklist' &&
					$this->inFilterValues( $bucket['key'], $filterValues )
				) {
					continue;
				}

				$filterCfg[$key] = [
					'buckets' => $this->formatBuckets( $bucket['value']['buckets'] ),
					'label' => $bucket['key'],
					'valueLabel' => $bucket['key'] . ':',
					'isANDEnabled' => 0,
					'group' => 'smwproperty'
				];
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function formatFulltextResult(
		array &$result, SearchResult $resultObject, ISearchSource $source, Lookup $lookup
	): void {
		if ( !( $source instanceof WikiPages ) ) {
			return;
		}

		if ( $this->isSemanticFilterSet( $lookup ) === false ) {
			return;
		}

		$lang = RequestContext::getMain()->getLanguage();
		$user = RequestContext::getMain()->getUser();

		$smwProperties = $resultObject->getSourceParam( 'smwproperty' );
		$smwPropertyValues = [];
		foreach ( $smwProperties as $property ) {
			$value = $property['value'];
			if ( $property['type'] === 'datetime' ) {
				$value = $lang->userTimeAndDate( $property['value'], $user );
			}

			if ( $property['type'] === 'boolean' ) {
				$value = $this->getBooleanLabel( $value );
			}

			if ( is_array( $value ) ) {
				$value = implode( ';', $value );
			}
			$smwPropertyValues[] = wfMessage(
				'bs-smwconnector-extendedsearch-smwproperty-result-value',
				$property['name'],
				$value
			)->plain();
		}
		$msg = wfMessage( 'bs-extendedsearch-search-center-result-smwproperty-label' );
		$boldLabel = "<b>" . $msg->plain() . "</b>";
		$result['smwproperty'] = $boldLabel . implode( ', ', $smwPropertyValues );
	}

	/**
	 * @inheritDoc
	 */
	public function formatAutocompleteResults( array &$results, array $searchData ): void {
		// NOOP
	}

	/**
	 * @inheritDoc
	 */
	public function modifyResultStructure( array &$resultStructure, ISearchSource $source ): void {
		if ( !( $source instanceof WikiPages ) ) {
			return;
		}
		$resultStructure['secondaryInfos']['bottom']['items'][] = [
			"name" => "smwproperty",
			"nolabel" => 1
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getLookupModifiers( Lookup $lookup, IContextSource $context ): array {
		return [
			new AddSMWAggregation( $lookup, $context ),
			new AddSourceFields( $lookup, $context ),
			new ParseSMWFilters( $lookup, $context ),
		];
	}

	/**
	 * Gets all semantic properties for a given page
	 * and sets correctly formatted array of values to be indexed as class variable
	 *
	 * @param \WikiPage $wikipage
	 */
	private function getSemanticData( $wikipage ) {
		$subject = \SMW\DIWikiPage::newFromTitle( $wikipage->getTitle() );
		$store = \SMW\StoreFactory::getStore();

		$properties = $store->getProperties( $subject );

		$smwData = [];
		foreach ( $properties as $property ) {
			if ( $property->isUserDefined() == false ) {
				// If only user properties are wanted
				// continue;
			}
			$name = $property->getKey();
			$values = $store->getPropertyValues( $subject, $property );
			$label = $property->getLabel() ?: $name;

			$type = 'string';
			$parsedValues = [];
			foreach ( $values as $value ) {
				$parsedValues[] = $this->parseSemanticValue( $value, $type );
			}

			if ( count( $parsedValues ) == 1 ) {
				// No need to store array for single value
				$parsedValues = $parsedValues[0];
			}

			$smwData[] = [
				"name" => $label,
				"value" => $parsedValues,
				"type" => $type
			];
		}
		$this->semanticData = $smwData;
	}

	/**
	 *
	 * @param \SMWDataItem $value
	 * @param string &$type
	 * @return string
	 */
	private function parseSemanticValue( $value, &$type ) {
		if ( $value instanceof \SMW\DIWikiPage && $value->getTitle() instanceof \Title ) {
			$type = 'title';
			return $value->getTitle()->getPrefixedText();
		} elseif ( $value instanceof \SMWDINumber ) {
			$type = 'numeric';
			return (string)$value->getNumber();
		} elseif ( $value instanceof \SMWDIBoolean ) {
			$type = 'boolean';
			return (string)$value->getBoolean() ? 1 : 0;
		} elseif ( $value instanceof \SMWDITime ) {
			$type = 'datetime';
			return $value->getMwTimestamp( TS_ISO_8601 );
		} else {
			// Use default serialization
			return $value->__toString();
		}
	}

	/**
	 *
	 * @return array
	 */
	private function getSemanticValueArray() {
		$valueArray = [];
		foreach ( $this->semanticData as $smwDataItem ) {
			$value = $smwDataItem['value'];
			if ( !is_array( $value ) ) {
				$value = [ $value ];
			}

			foreach ( $value as $singleValue ) {
				$valueArray[] = $smwDataItem['name'] . '|' . $singleValue;
			}
		}
		return $valueArray;
	}

	/**
	 * @param array $buckets
	 * @return array
	 */
	private function formatBuckets( $buckets ) {
		foreach ( $buckets as &$bucket ) {
			if ( $bucket['type'] === 'title' ) {
				$bucket['label'] = $this->checkAndFormatUsername( $bucket['key'] );
			}
		}

		return $buckets;
	}

	/**
	 * Check if page is a user page, and try to get user display text
	 *
	 * @param string $titleKey
	 * @return string
	 */
	private function checkAndFormatUsername( $titleKey ) {
		$title = Title::newFromText( $titleKey );
		if ( !$title instanceof Title || $title->getNamespace() !== NS_USER ) {
			return $titleKey;
		}

		$services = MediaWikiServices::getInstance();
		if ( $services->getUserNameUtils()->isIP( $title->getDBkey() ) ) {
			return Message::newFromKey( "bs-smwconnector-extendedsearch-anon-user-label" )->text();
		}
		$user = $services->getUserFactory()->newFromName( $title->getDBkey() );
		if ( $user instanceof User ) {
			/** @var UtilityFactory $utilFactory */
			$utilFactory = $services->getService( 'BSUtilityFactory' );
			return $utilFactory->getUserHelper( $user )->getDisplayName();
		}

		return $titleKey;
	}

	/**
	 *
	 * @param bool $boolValue
	 * @return string
	 */
	protected function getBooleanLabel( $boolValue ) {
		if ( $boolValue ) {
			return wfMessage( 'bs-smwconnector-extendedsearch-boolean-property-true' )->plain();
		} else {
			return wfMessage( 'bs-smwconnector-extendedsearch-boolean-property-false' )->plain();
		}
	}

	/**
	 * @param Lookup $lookup
	 *
	 * @return bool
	 */
	protected function isSemanticFilterSet( Lookup $lookup ) {
		$filters = $lookup->getFilters();

		if ( !isset( $filters['terms'] ) ) {
			return false;
		}

		foreach ( $filters['terms'] as $key => $value ) {
			$decodedKey = base64_decode( $key );
			if ( strpos( $decodedKey, 'smwproperty:' ) === 0 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if prop name is in the list of allowed/blocked props
	 *
	 * @param string $key
	 * @param array $values
	 * @return bool
	 */
	private function inFilterValues( $key, $values ) {
		foreach ( $values as $value ) {
			if ( preg_match( "/$value/", $key ) ) {
				return true;
			}
		}

		return false;
	}
}
