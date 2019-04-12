<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\Formatter;

use BS\ExtendedSearch\Source\Formatter\WikiPageFormatter;

class SMWWikiPageFormatter extends WikiPageFormatter {
	public function getResultStructure( $defaultResultStructure = [] ) {
		$resultStructure = parent::getResultStructure( $defaultResultStructure );

		$resultStructure['secondaryInfos']['bottom']['items'][] = [
			"name" => "smwproperty",
			"nolabel" => 1
		];

		return $resultStructure;
	}

	public function format( &$result, $resultObject ) {
		if ( $this->source->getTypeKey() != $resultObject->getType() ) {
			return;
		}

		parent::format( $result, $resultObject );
		if ( $this->isSemanticFilterSet() === false ) {
			return;
		}

		$lang = $this->getContext()->getLanguage();
		$user = $this->getContext()->getUser();

		$smwProperties = $result['smwproperty'];
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
		$boldLabel = "<b>" . wfMessage( 'bs-extendedsearch-search-center-result-smwproperty-label' )->plain() . "</b>";
		$result['smwproperty'] = $boldLabel . implode( ', ', $smwPropertyValues );
	}

	/**
	 * Parses aggs for smwproperty field and converts it
	 * to usual format for the UI
	 *
	 * @param array &$aggs
	 * @param array &$filterCfg
	 * @param bool $fieldsWithANDEnabled
	 */
	public function formatFilters( &$aggs, &$filterCfg, $fieldsWithANDEnabled = false ) {
		if ( isset( $aggs['smwproperty'] ) ) {
			$smwAgg = $aggs['smwproperty'];
			unset( $aggs['smwproperty'] );

			if ( $smwAgg['doc_count'] === 0 ) {
				return;
			}

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

				$filterCfg[$key] = [
					'buckets' => $bucket['value']['buckets'],
					'label' => $bucket['key'],
					'valueLabel' => $bucket['key'] . ':',
					'isANDEnabled' => 0,
					'group' => 'smwproperty'
				];
			}
		}
	}

	protected function getBooleanLabel( $boolValue ) {
		if ( $boolValue ) {
			return wfMessage( 'bs-smwconnector-extendedsearch-boolean-property-true' )->plain();
		} else {
			return wfMessage( 'bs-smwconnector-extendedsearch-boolean-property-false' )->plain();
		}
	}

	protected function isSemanticFilterSet() {
		$filters = $this->lookup->getFilters();

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

}
