<?php

namespace BlueSpice\SMWConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TagDroplet;
use Message;

class DecisionOverviewDroplet extends TagDroplet {

	/**
	 *
	 * @return string
	 */
	protected function getTagName(): string {
		return 'decisionOverview';
	}

	/**
	 *
	 * @return array
	 */
	protected function getAttributes(): array {
		return [
			'namespace',
			'category',
			'page'
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'bs-smwconnector-decision-overview-droplet-title' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'bs-smwconnector-decision-overview-droplet-description' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-decision-overview';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.BSSMWConnector.decisionOverview.visualEditor' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content', 'featured' ];
	}

	/**
	 * @return bool
	 */
	protected function hasContent(): bool {
		return false;
	}

	/**
	 * @return string|null
	 */
	public function getVeCommand(): ?string {
		return 'decisionOverviewCommand';
	}
}
