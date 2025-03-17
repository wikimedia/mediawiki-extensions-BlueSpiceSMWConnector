<?php
use MediaWiki\MediaWikiServices;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

/**
 * Maintenance script for generating a JavaScript script, with which one can
 * rerender pages with semantic dependencies to make the display up to date.
 * This script helps e.g when a semantic property of one page is defined out
 * of another semantic property of the same page.
 *
 * Before running this script, you need to enable the query dependency links
 * store function of Semantic MediaWiki:
 * - set `$smwgEnabledQueryDependencyLinksStore = true;` (in LocalSettings.php)
 * - run `update.php` to create the necessary database tables
 *
 * Example for Arguments:
 * - entrance: compulsory, e.g `https://example.company.com/w/index.php`
 * - prefix: optional, case-sensitive, e.g `WantedNamespace:` will only include
 *           pages in namespace `WantedNamespace` for the rerendering
 * - lag: optional, e.g `2000` will keep 2 seconds delay between each request
 *
 * After running this script successfully, you will get a JavaScript script
 * that can be executed in the browser console to rerender all pages.
 * It is recommended that you run this script in a browser, with which you have
 * logged in your wiki already.
 *
 * @license GPL-3.0-only
 * @author Hua Jing (Hallo Welt! GmbH)
 */
class GenerateRerenderScript extends Maintenance {
	/** @var string */
	private $prefix = '';

	/** @var int */
	private $lag = 2500;

	public function __construct() {
		parent::__construct();
		$this->requireExtension( 'SemanticMediaWiki' );
		$this->addDescription( 'Query SMW objects and their links' );
		$this->addOption(
			'entrance',
			'The entrance link to index.php of your installation',
			true,
			true
		);
		$this->addOption(
			'prefix',
			'The prefix to filter the page titles',
			false,
			true
		);
		$this->addOption(
			'lag',
			'The delay between each purge request in milliseconds',
			false,
			true
		);
	}

	public function execute() {
		if ( substr( $this->getOption( 'entrance' ), -9 ) !== 'index.php' ) {
			$this->error(
				"Invalid entrance link: it should end with 'index.php'\n"
			);
			return false;
		}
		if ( !$GLOBALS['smwgEnabledQueryDependencyLinksStore'] ) {
			$this->error(
				"Query dependency links store is disabled, unable to proceed\n"
				. "Please set \$smwgEnabledQueryDependencyLinksStore = true; "
				. "in e.g LocalSettings.php\n"
			);
			return false;
		}
		$dbr = $this->getDB( DB_REPLICA );
		if ( !$dbr->tableExists( 'smw_query_links', __METHOD__ ) ) {
			$this->error(
				"Table 'smw_query_links' does not exist, unable tp proceed\n"
				. "Please run `update.php` to create the necessary db tables\n"
			);
			return false;
		}
		$results = $dbr->select(
			[ 'smw_object_ids', 'smw_query_links' ],
			[ 'DISTINCT smw_title', 'smw_namespace' ],
			[ 'smw_query_links.s_id = smw_object_ids.smw_id' ],
			__METHOD__,
			[ 'ORDER BY' => 'smw_namespace' ]
		);

		if ( $this->hasOption( 'prefix' ) ) {
			$this->prefix = $this->getOption( 'prefix' );
		}
		$outputData = [];
		$i = 1;
		foreach ( $results as $row ) {
			$namespaceName = MediaWikiServices::getInstance()
			->getNamespaceInfo()
			->getCanonicalName( $row->smw_namespace );
			if ( $namespaceName === false ) {
				$namespaceName = 'Main';
			}
			$formattedTitle = $row->smw_title;
			$fullPageTitle = ( $namespaceName === 'Main' )
				? $formattedTitle
				: "$namespaceName:$formattedTitle";
			if ( $this->prefix === '' ) {
				$outputData[$i] = $fullPageTitle;
				$i++;
			} elseif ( strpos( $fullPageTitle, $this->prefix ) === 0 ) {
				$outputData[$i] = $fullPageTitle;
				$i++;
			}
		}

		$this->output(
			"\n// Please copy code below to the JavaScript"
			. " console of your browser:\n"
		);
		$this->output(
			"const pages = "
			. json_encode( array_values( $outputData ), JSON_PRETTY_PRINT )
			. ";\n"
		);
		$this->output(
			"const entrance = `"
			. $this->getOption( 'entrance' )
			. "`;\n"
		);
		if ( $this->hasOption( 'lag' ) ) {
			$this->lag = intval( $this->getOption( 'lag' ) );
		}
		$this->output( "const lag = " . $this->lag . ";\n" );
		$this->output( <<<'JS'
const getTimestamp = () => {
    const now = new Date();
    return now.toISOString().replace('T', ' ').replace(/\..+/, '');
};
const purgeWithDelay = (pageTitle, index, total) => {
    return new Promise((resolve) => {
        setTimeout(() => {
            const encodedTitle = encodeURIComponent(pageTitle);
            const url = entrance + `?title=${encodedTitle}&action=purge`;
            fetch(url, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
            .then(response => {
		console.log(
                    `[${getTimestamp()}] [${index + 1}/${total}] ${pageTitle} purged, (Status: ${response.status})`
		);
            })
            .catch(error => {
                console.error(`[${getTimestamp()}] [${index + 1}/${total}] error purging ${pageTitle}:`, error);
            })
            .finally(() => resolve());
        }, lag);
    });
};
async function rerenderPages() {
    const total = pages.length;
    console.log(`[${getTimestamp()}] Starting to rerender ${total} pages (action=purge via POST)...`);
    for (let i = 0; i < pages.length; i++) {
        await purgeWithDelay(pages[i], i, total);
    }
    console.log(`[${getTimestamp()}] Finished rerendering all pages!`);
}
JS );
		$this->output( "\nrerenderPages();\n\n" );
		return true;
	}
}

$maintClass = GenerateRerenderScript::class;
require_once RUN_MAINTENANCE_IF_MAIN;
