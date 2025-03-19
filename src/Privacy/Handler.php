<?php

namespace BlueSpice\SMWConnector\Privacy;

use BlueSpice\Privacy\IPrivacyHandler;
use MediaWiki\Status\Status;
use MediaWiki\User\User;
use Wikimedia\Rdbms\IDatabase;

class Handler implements IPrivacyHandler {
	/** @var IDatabase */
	protected $db;

	/**
	 *
	 * @param IDatabase $db
	 */
	public function __construct( IDatabase $db ) {
		$this->db = $db;
	}

	/**
	 *
	 * @param string $oldUsername
	 * @param string $newUsername
	 * @return Status
	 */
	public function anonymize( $oldUsername, $newUsername ) {
		$this->db->update(
			'smw_object_ids',
			[
				'smw_title' => $newUsername,
				'smw_sortkey' => $newUsername,
				'smw_sort' => $newUsername
			],
			[
				// Just handle links to user pages, not random string that match the username
				'smw_title' => $oldUsername,
				'smw_namespace' => NS_USER
			],
			__METHOD__
		);
		return Status::newGood();
	}

	/**
	 *
	 * @param User $userToDelete
	 * @param User $deletedUser
	 * @return Status
	 */
	public function delete( User $userToDelete, User $deletedUser ) {
		return $this->anonymize( $userToDelete->getName(), $deletedUser->getName() );
	}

	/**
	 *
	 * @param array $types
	 * @param string $format
	 * @param User $user
	 * @return Status
	 */
	public function exportData( array $types, $format, User $user ) {
		return Status::newGood( [] );
	}
}
