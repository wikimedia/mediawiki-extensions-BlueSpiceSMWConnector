<?php
/**
 * Hook handler base class for SMW:RevisionGuard:IsApprovedRevision hook
 * (https://doc.semantic-mediawiki.org/md_content_extensions_SemanticMediaWiki_docs_technical_hooks_hook_8revisionguard_8isapprovedrevision.html)
 */
namespace BlueSpice\SMWConnector\Hook\SMWRevisionGuard;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Title\Title;

abstract class IsApprovedRevision extends Hook {

	/** @var Title */
	protected $title;

	/** @var int */
	protected $latestRevID;

	/**
	 * @param Title $title
	 * @param int $latestRevID
	 * @return bool
	 */
	public static function callback( Title $title, $latestRevID ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$latestRevID
		);
		return $hookHandler->process();
	}

	/**
	 * SMWRevisionGuardIsApprovedRevision constructor.
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title $title
	 * @param int $latestRevID
	 */
	public function __construct( $context, $config, Title $title, $latestRevID ) {
		parent::__construct( $context, $config );
		$this->title = $title;
		$this->latestRevID = $latestRevID;
	}

}
