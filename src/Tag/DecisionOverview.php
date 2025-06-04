<?php

namespace BlueSpice\SMWConnector\Tag;

use MediaWiki\Language\RawMessage;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MWStake\MediaWiki\Component\FormEngine\FormLoaderSpecification;
use MWStake\MediaWiki\Component\GenericTagHandler\ClientTagSpecification;
use MWStake\MediaWiki\Component\GenericTagHandler\GenericTag;
use MWStake\MediaWiki\Component\GenericTagHandler\ITagHandler;
use MWStake\MediaWiki\Component\GenericTagHandler\MarkerType;

class DecisionOverview extends GenericTag {

	public const ATTR_CATEGORIES = 'categories';
	public const ATTR_NAMESPACES = 'namespaces';
	public const ATTR_PREFIX = 'prefix';

	/**
	 * @inheritDoc
	 */
	public function getTagNames(): array {
		return [ 'decisionoverview' ];
	}

	/**
	 * @inheritDoc
	 */
	public function hasContent(): bool {
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getMarkerType(): MarkerType {
		return new MarkerType\NoWiki();
	}

	/**
	 * @inheritDoc
	 */
	public function getContainerElementName(): ?string {
		return 'div';
	}

	/**
	 * @inheritDoc
	 */
	public function getHandler( MediaWikiServices $services ): ITagHandler {
		return new DecisionOverviewHandler( $services->getContentLanguage() );
	}

	/**
	 * @inheritDoc
	 */
	public function getParamDefinition(): ?array {
		return [
			static::ATTR_CATEGORIES => [
				'type' => 'category-list',
				'separator' => '|'
			],
			static::ATTR_NAMESPACES => [
				'type' => 'namespace-list',
				'separator' => '|'
			],
			static::ATTR_PREFIX => [
				'type' => 'string',
				'default' => ''
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getClientTagSpecification(): ClientTagSpecification|null {
		return new ClientTagSpecification(
			'DecisionOverview',
			new RawMessage( '' ),
			new FormLoaderSpecification(
				'bs.swmconnector.ui.DecisionOverviewForm',
				[ 'ext.BSSMWConnector.decisionoverview.form' ]
			),
			Message::newFromKey( 'bs-smwconnector-decision-overview-title' )
		);
	}
}
