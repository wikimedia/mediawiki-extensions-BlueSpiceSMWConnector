<?php

namespace BlueSpice\SMWConnector\Tag;

use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\Tag\GenericHandler;
use BlueSpice\Tag\MarkerType\NoWiki;
use BlueSpice\Tag\Tag;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Parser;
use PPFrame;

class DecisionOverview extends Tag {

	public const ATTR_CATEGORIES = 'categories';
	public const ATTR_NAMESPACES = 'namespaces';
	public const ATTR_PREFIX = 'prefix';

	/** @var MediaWikiServices */
	private $services;

	public function __construct() {
		$this->services = MediaWikiServices::getInstance();
	}

	/**
	 *
	 * @return NoWiki
	 */
	public function getMarkerType() {
		return new NoWiki();
	}

	/**
	 *
	 * @return string
	 */
	public function getContainerElementName() {
		return GenericHandler::TAG_DIV;
	}

	/**
	 * @param string $processedInput
	 * @param array $processedArgs
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @return SmartListHandler
	 */
	public function getHandler( $processedInput, array $processedArgs, Parser $parser, PPFrame $frame ) {
		$language = $this->services->getContentLanguage();

		return new DecisionOverviewHandler(
			$processedInput,
			$processedArgs,
			$parser,
			$frame,
			$language
		);
	}

	/**
	 * @return string[]
	 */
	public function getTagNames() {
		return [
			'decisionoverview'
		];
	}

	/**
	 * @return IParamDefinition[]
	 */
	public function getArgsDefinitions() {
		return [
			new ParamDefinition(
				ParamType::STRING,
				static::ATTR_CATEGORIES,
				''
			),
			new ParamDefinition(
				ParamType::STRING,
				static::ATTR_NAMESPACES,
				''
			),
			new ParamDefinition(
				ParamType::STRING,
				static::ATTR_PREFIX,
				''
			)
		];
	}
}
