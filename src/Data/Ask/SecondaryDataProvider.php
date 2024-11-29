<?php

namespace BlueSpice\SMWConnector\Data\Ask;

use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\ISecondaryDataProvider;
use MWStake\MediaWiki\Component\DataStore\Record;
use Title;

class SecondaryDataProvider implements ISecondaryDataProvider {
	/** @var Schema */
	protected $schema;

	/**
	 * SecondaryDataProvider constructor.
	 * @param Schema $schema
	 */
	public function __construct( Schema $schema ) {
		$this->schema = $schema;
	}

	/**
	 *
	 * @param Record[] $dataSets
	 * @return Record[]
	 */
	public function extend( $dataSets ) {
		foreach ( $dataSets as $record ) {
			$title = Title::newFromText( $record->get( Schema::PAGE ) );
			if ( $title instanceof Title ) {
				$this->addFieldsForTitle( $title, $record );
				$record->set( Schema::PAGE_LINK, $this->getLinkForPage( $title ) );
			}

			foreach ( (array)$this->schema as $key => $def ) {
				if ( isset( $def[Schema::PROPERTY_TYPE] ) && $def[Schema::PROPERTY_TYPE] === '_wpg' ) {
					$this->setWikiPageLinks( $key, $record->get( $key ), $record );
				}
			}
		}

		return $dataSets;
	}

	/**
	 * Allow sub-classes to add fields based on title without having to
	 * re-initialize it
	 *
	 * @param Title $title
	 * @param Record &$record
	 */
	protected function addFieldsForTitle( Title $title, &$record ) {
		// STUB
	}

	/**
	 * @param Title $title
	 * @return string|null
	 */
	protected function getLinkForPage( Title $title ) {
		return MediaWikiServices::getInstance()->getLinkRenderer()->makeLink( $title );
	}

	/**
	 * @param string $key
	 * @param string|string[] $value
	 * @param Record &$record
	 */
	private function setWikiPageLinks( $key, $value, Record &$record ) {
		$value = is_array( $value ) ? $value : [ $value ];
		$links = [];
		foreach ( $value as $titleKey ) {
			$title = Title::newFromText( $titleKey );
			if ( !$title instanceof Title ) {
				continue;
			}
			$links[] = $this->getLinkForPage( $title );
		}

		if ( count( $links ) === 1 ) {
			$links = $links[0];
		}

		if ( $key === 'page' ) {
			$record->set( 'page_url', $title->getLocalURL() );
		}
		$record->set( "{$key}_link", $links );
	}
}
