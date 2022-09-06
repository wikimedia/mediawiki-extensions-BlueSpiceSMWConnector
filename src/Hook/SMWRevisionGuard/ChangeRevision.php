<?php
/**
 * Hook handler base class for SMW:RevisionGuard:ChangeRevision hook
 * (https://doc.semantic-mediawiki.org/md_content_extensions_SemanticMediaWiki_docs_technical_hooks_hook_8revisionguard_8isapprovedrevision.html)
 */
namespace BlueSpice\SMWConnector\Hook\SMWRevisionGuard;

use BlueSpice\Hook;
use Config;
use IContextSource;
use MediaWiki\Revision\RevisionRecord;
use Title;

abstract class ChangeRevision extends Hook {

	/** @var Title */
	protected $title;

	/** @var RevisionRecord|null */
	protected $revision;

	/**
	 * @param Title $title
	 * @param RevisionRecord|null &$revision
	 * @return bool
	 */
	public static function callback( Title $title, &$revision ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$revision
		);
		return $hookHandler->process();
	}

	/**
	 * SMWRevisionGuardIsApprovedRevision constructor.
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title $title
	 * @param RevisionRecord &$revision
	 */
	public function __construct( $context, $config, Title $title, &$revision ) {
		parent::__construct( $context, $config );
		$this->title = $title;
		$this->revision = &$revision;
	}

}
