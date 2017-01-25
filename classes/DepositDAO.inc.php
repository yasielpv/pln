<?php

/**
 * @file plugins/generic/pln/classes/DepositDAO.inc.php
 *
 * Copyright (c) 2013-2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DepositDAO
 * @ingroup plugins_generic_pln_classes
 *
 * @brief Operations for adding a PLN deposit
 */

import('lib.pkp.classes.db.DAO');

class DepositDAO extends DAO {

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Construct a new deposit object.
	 * @return Deposit
	 */
	function newDataObject() {
		return new Deposit(null);
	}

	/**
	 * Retrieve deposit by ID.
	 * @param $depositId int
	 * @return Deposit Object
	 */
	function getById($depositId) {
		$result = $this->retrieve(
			'SELECT * FROM pln_deposits WHERE deposit_id = ?',
			array (
				(int) $depositId
			)
		);
		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		return $returner;
	}

	/**
	 * Insert deposit object
	 * @param $deposit Deposit
	 * @return int inserted Deposit id
	 */
	function insertObject($deposit) {
		$this->update(
			sprintf('
				INSERT INTO pln_deposits
					(journal_id,
					uuid,
					status,
					date_status,
					date_created,
					date_modified)
				VALUES
					(?, ?, ?, %s, NOW(), %s)',
				$this->datetimeToDB($deposit->getLastStatusDate()),
				$this->datetimeToDB($deposit->getDateModified())
			),
			array(
				(int) $deposit->getJournalId(),
				$deposit->getUUID(),
				(int) $deposit->getStatus()
			)
		);
		$deposit->setId($this->getInsertId());
		return $deposit->getId();
	}

	/**
	 * Update deposit
	 * @param $deposit Deposit
	 */
	function updateObject($deposit) {
		$this->update(
			sprintf('
				UPDATE pln_deposits SET
					journal_id = ?,
					uuid = ?,
					status = ?,
					date_status = %s,
					date_created = %s,
					date_modified = NOW()
				WHERE deposit_id = ?',
				$this->datetimeToDB($deposit->getLastStatusDate()),
				$this->datetimeToDB($deposit->getDateCreated())
			),
			array(
				(int) $deposit->getJournalId(),
				$deposit->getUUID(),
				(int) $deposit->getStatus(),
				(int) $deposit->getId()
			)
		);
	}

	/**
	 * Delete deposit
	 * @param $deposit Deposit
	 */
	function deleteObject($deposit) {
		$deposit_object_dao = DAORegistry::getDAO('DepositObjectDAO');
		foreach($deposit->getDepositObjects() as $deposit_object) {
			$deposit_object_dao->deleteObject($deposit_object);
		}

		$this->update(
			'DELETE from pln_deposits WHERE deposit_id = ?',
			(int) $deposit->getId()
		);
	}

	/**
	 * Get the ID of the last inserted deposit.
	 * @return int
	 */
	function getInsertId() {
		return $this->_getInsertId('pln_deposits', 'deposit_id');
	}

	/**
	 * Internal function to return a deposit from a row.
	 * @param $row array
	 * @return Deposit
	 */
	function _fromRow($row) {
		$deposit = $this->newDataObject();
		$deposit->setId($row['deposit_id']);
		$deposit->setJournalId($row['journal_id']);
		$deposit->setUUID($row['uuid']);
		$deposit->setStatus($row['status']);
		$deposit->setLastStatusDate($this->datetimeFromDB($row['date_status']));
		$deposit->setDateCreated($this->datetimeFromDB($row['date_created']));
		$deposit->setDateModified($this->datetimeFromDB($row['date_modified']));

		HookRegistry::call('DepositDAO::_fromRow', array(&$deposit, &$row));

		return $deposit;
	}

	/**
	 * Retrieve a deposit by deposit uuid and journal id.
	 * @param $journalId int
	 * @param $depositUuid string
	 * @return Deposit
	 */
	function getByUUID($journalId, $depositUuid) {
		$result = $this->retrieve(
			'SELECT * FROM pln_deposits WHERE journal_id = ? AND uuid = ?',
			array (
				(int) $journalId,
				$depositUuid
			)
		);
		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		return $returner;
	}

	/**
	 * Retrieve all deposits.
	 * @param $journalId int
	 * @return array Deposit
	 */
	function getByJournalId($journalId, $dbResultRange = null) {
		$result = $this->retrieveRange(
			'SELECT * FROM pln_deposits WHERE journal_id = ? ORDER BY deposit_id',
			array (
				(int) $journalId
			),
			$dbResultRange
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Retrieve all newly-created deposits (ones with new status)
	 * @param $journalId int
	 * @return array Deposit
	 */
	function getNew($journalId) {
		$result = $this->retrieve(
			'SELECT * FROM pln_deposits WHERE journal_id = ? AND status = ?',
			array(
				(int) $journalId,
				(int) PLN_PLUGIN_DEPOSIT_STATUS_NEW
			)
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Retrieve all deposits that need packaging
	 * @param $journalId int
	 * @return array Deposit
	 */
	function getNeedTransferring($journalId) {
		$result = $this->retrieve(
			'SELECT * FROM pln_deposits AS d WHERE d.journal_id = ? AND d.status & ? = 0 AND d.status & ? = 0',
			array (
				(int) $journalId,
				(int) PLN_PLUGIN_DEPOSIT_STATUS_TRANSFERRED,
				(int) PLN_PLUGIN_DEPOSIT_STATUS_LOCKSS_AGREEMENT
			)
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Retrieve all deposits that need packaging
	 * @param $journalId int
	 * @return array Deposit
	 */
	function getNeedPackaging($journalId) {
		$result = $this->retrieve(
			'SELECT * FROM pln_deposits AS d WHERE d.journal_id = ? AND d.status & ? = 0 AND d.status & ? = 0',
			array(
				(int) $journalId,
				(int) PLN_PLUGIN_DEPOSIT_STATUS_PACKAGED,
				(int) PLN_PLUGIN_DEPOSIT_STATUS_LOCKSS_AGREEMENT
			)
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Retrieve all deposits that need a status update
	 * @param $journalId int
	 * @return array Deposit
	 */
	function getNeedStagingStatusUpdate($journalId) {
		$result = $this->retrieve(
			'SELECT * FROM pln_deposits AS d WHERE d.journal_id = ? AND d.status & ? <> 0 AND d.status & ? = 0',
			array (
				(int) $journalId,
				(int) PLN_PLUGIN_DEPOSIT_STATUS_TRANSFERRED,
				(int) PLN_PLUGIN_DEPOSIT_STATUS_LOCKSS_AGREEMENT
			)
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Delete deposits by journal id
	 * @param $journalId
	 */
	function deleteByJournalId($journalId) {
		$deposits = $this->getByJournalId($journalId);
		foreach($deposits as $deposit) {
			$this->deleteDeposit($deposit);
		}
	}
}

?>
