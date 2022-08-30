<?php

namespace BlueSpice\SMWConnector\ExtendedSearch\Source\Formatter;

use BlueSpice\UtilityFactory;
use BS\ExtendedSearch\Source\Formatter\WikiPageFormatter;
use MediaWiki\MediaWikiServices;
use Message;
use Title;
use User;

class SMWWikiPageFormatter extends WikiPageFormatter {
	/**
	 *
	 * @param array $defaultResultStructure
	 * @return array
	 */
	public function getResultStructure( $defaultResultStructure = [] ) {
		$resultStructure = parent::getResultStructure( $defaultResultStructure );

		$resultStructure['secondaryInfos']['bottom']['items'][] = [
			"name" => "smwproperty",
			"nolabel" => 1
		];

		return $resultStructure;
	}

	/**
	 *
	 * @param array &$result
	 * @param \Elastica\Result $resultObject
	 */
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
		$msg = wfMessage( 'bs-extendedsearch-search-center-result-smwproperty-label' );
		$boldLabel = "<b>" . $msg->plain() . "</b>";
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
		$user = User::newFromName( $title->getDBkey() );
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
	 *
	 * @return bool
	 */
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
