<?php

/**
 * @file classes/DepositObject.inc.php
 *
 * Copyright (c) 2013-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class DepositObject
 * @brief Basic class describing a deposit stored in the PLN
 */

class DepositObject extends DataObject {
	/**
	 * Get the content object that's referenced by this deposit object
	 * @return Object (Issue,Article)
	 */
	public function getContent() {
		switch ($this->getObjectType()) {
			case PLN_PLUGIN_DEPOSIT_OBJECT_ISSUE:
				$issueDao = DAORegistry::getDAO('IssueDAO');
				return $issueDao->getIssueById($this->getObjectId(),$this->getJournalId());
			case PLN_PLUGIN_DEPOSIT_OBJECT_ARTICLE:
				$articleDao = DAORegistry::getDAO('ArticleDAO');
				return $articleDao->getArticle($this->getObjectId(),$this->getJournalId());
			default: assert(false);
		}
	}

	/**
	 * Set the content object that's referenced by this deposit object
	 * @param $content Object (Issue,Article)
	 */
	public function setContent($content) {
		switch (get_class($content)) {
			case PLN_PLUGIN_DEPOSIT_OBJECT_ISSUE:
			case PLN_PLUGIN_DEPOSIT_OBJECT_ARTICLE:
				$this->setObjectId($content->getId());
				$this->setObjectType(get_class($content));
				break;
			default: assert(false);
		}
	}

	/**
	 * Get type of the object being referenced by this deposit object
	 * @return string
	 */
	public function getObjectType() {
		return $this->getData('objectType');
	}

	/**
	 * Set type of the object being referenced by this deposit object
	 * @param string
	 */
	public function setObjectType($objectType) {
		$this->setData('objectType', $objectType);
	}

	/**
	 * Get the id of the object being referenced by this deposit object
	 * @return int
	 */
	public function getObjectId() {
		return $this->getData('objectId');
	}

	/**
	 * Set the id of the object being referenced by this deposit object
	 * @param int
	 */
	public function setObjectId($objectId) {
		$this->setData('objectId', $objectId);
	}

	/**
	 * Get the journal id of this deposit object
	 * @return int
	 */
	public function getJournalId() {
		return $this->getData('journalId');
	}

	/**
	 * Set the journal id of this deposit object
	 * @param int
	 */
	public function setJournalId($journalId) {
		$this->setData('journalId', $journalId);
	}

	/**
	 * Get the id of the deposit which includes this deposit object
	 * @return int
	 */
	public function getDepositId() {
		return $this->getData('depositId');
	}

	/**
	 * Set the id of the deposit which includes this deposit object
	 * @param int
	 */
	public function setDepositId($depositId) {
		$this->setData('depositId', $depositId);
	}

	/**
	 * Get the date of deposit object creation
	 * @return DateTime
	 */
	public function getDateCreated() {
		return $this->getData('dateCreated');
	}

	/**
	 * Set the date of deposit object creation
	 * @param $dateCreated DateTime
	 */
	public function setDateCreated($dateCreated) {
		$this->setData('dateCreated', $dateCreated);
	}

	/**
	 * Get the modification date of the deposit object
	 * @return DateTime
	 */
	public function getDateModified() {
		return $this->getData('dateModified');
	}

	/**
	 * Set the modification date of the deposit object
	 * @param $dateModified DateTime
	 */
	public function setDateModified($dateModified) {
		$this->setData('dateModified', $dateModified);
	}
}
