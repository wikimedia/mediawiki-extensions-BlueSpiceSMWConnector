<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\Formatter;

use BS\ExtendedSearch\Source\Formatter\WikiPageFormatter;
use BlueSpice\SMWConnector\ExtendedSearch\Source\SMWWikiPage;

class SMWWikiPageFormatter extends WikiPageFormatter {
	public function getResultStructure ( $defaultResultStructure = [] ) {
		$resultStructure = parent::getResultStructure( $defaultResultStructure );

		$resultStructure['secondaryInfos']['bottom']['items'][] = [
			"name" => "smwproperty",
			"nolabel" => 1
		];

		return $resultStructure;
	}

	public function format( &$result, $resultObject ) {
		if( $this->source->getTypeKey() != $resultObject->getType() ) {
			return;
		}

		parent::format( $result, $resultObject );

		$lang = $this->getContext()->getLanguage();
		$user = $this->getContext()->getUser();

		$smwProperties = $result['smwproperty'];
		$smwPropertyValues = [];
		foreach( $smwProperties as $property ) {
			$value = $property['value'];
			if( $property['type'] === 'datetime' ) {
				$value = $lang->userTimeAndDate( $property['value'], $user );
			}
			if( is_array( $value ) ) {
				$value = implode( ',', $value );
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
	 * @param array $aggs
	 * @param array $filterCfg
	 * @param bool $fieldsWithANDEnabled
	 */
	public function formatFilters( &$aggs, &$filterCfg, $fieldsWithANDEnabled = false ) {
		if( isset( $aggs['smwproperty'] ) ) {
			$smwAgg = $aggs['smwproperty'];
			unset( $aggs['smwproperty'] );

			if( $smwAgg['doc_count'] === 0 ) {
				return;
			}

			$smwAgg = $smwAgg['name'];
			foreach( $smwAgg['buckets'] as $bucket ) {
				//First type of first key must be the type for all the keys
				$type = $bucket['value']['buckets'][0]['type']['buckets'][0]['key'];

				//We dont want to filer by dates, as range filters are not (yet) supported
				if( $type == 'datetime' ) {
					continue;
				}

				foreach( $bucket['value']['buckets'] as &$valueBucket ) {
					$valueBucket['type'] = $type;
				}
				//jQuery/Sizzle is very sensitive to what is an ID
				//of an element, encode value is always alphanumeric
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
}

