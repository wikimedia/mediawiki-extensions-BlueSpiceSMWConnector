<?php

require __DIR__ . '/../../../maintenance/Maintenance.php';

class ChangeSMWOffsetNamespaceIds extends Maintenance {

	protected $namespaceIdMap = [
		700 => 100,
		701 => 101,
		702 => 102,
		703 => 103,
		704 => 104,
		705 => 105,
		706 => 106
	];

	protected $tablesAndFields = [
		'archive' => [ 'ar_namespace' ],
		'page' => [ 'page_namespace' ],
		'pagelinks' => [ 'pl_namespace' ],
		'templatelinks' => [ 'tl_namespace' ],
		'logging' => [ 'log_namespace' ],
		'job' => [ 'job_namespace' ],
		'redirect' => [ 'rd_namespace' ],
		'recentchanges' => [ 'rc_namespace' ],
		'querycache' =>  [ 'qc_namespace' ],
		'querycachetwo' =>  [ 'qcc_namespace', 'qcc_namespacetwo' ],
		'protected_titles' => [ 'pt_namespace' ],
		'watchlist' => [ 'wl_namespace' ]
	];


	public function execute() {
		$this->output(
<<<HERE

WARNING: This script will perform changes directly on the database! If you have
not yet created a backup, please abort and do so now!

HERE
		);
		wfCountDown( 5 );

		$this->output( "Too late ..." );

		foreach( $this->tablesAndFields as $tableName => $fieldNames ) {
			foreach( $fieldNames as $fieldName ) {
				$this->output("\Processing $tableName.$fieldName ..." );
				$this->fixNamespaceIds(
					$tableName,
					$fieldName
				);
				$this->output( "\n" );
			}
		}
	}

	protected function fixNamespaceIds( $tableName, $fieldName ) {
		foreach( $this->namespaceIdMap as $oldNS => $newNS ) {
			$this->output( "\nNamespace '$oldNS': " );
			$numberOfEntriesToChange = $this->getDB( DB_SLAVE )
				->selectRowCount(
					$tableName,
					'*',
					[
						$fieldName => $oldNS
					]
				);
			if( $numberOfEntriesToChange === 0 ) {
				$this->output( "Nothing to do.");
				continue;
			}
			$this->output( "Found: $numberOfEntriesToChange\n");
			$this->output( "Changing ..." );

			$res = $this->getDB( DB_MASTER )->update(
				$tableName,
				[
					$fieldName => $newNS
				],
				[
					$fieldName => $oldNS
				]
			);
			$this->output( " done." );
		}
	}

}

$maintClass = 'ChangeSMWOffsetNamespaceIds';
require_once( RUN_MAINTENANCE_IF_MAIN );
