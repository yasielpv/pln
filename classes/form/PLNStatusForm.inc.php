<?php

/**
 * @file plugins/generic/pln/classes/form/PLNStatusForm.inc.php
 *
 * Copyright (c) 2013-2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PLNStatusForm
 * @ingroup plugins_generic_pln_classes_form
 *
 * @brief Form for journal managers to check PLN plugin status
 */

import('lib.pkp.classes.form.Form');

class PLNStatusForm extends Form {

	/**
	 * @var $_contextId int
	 */
	var $_contextId;

	/**
	 * @var $plugin Object
	 */
	var $_plugin;

	/**
	 * Constructor
	 * @param $plugin object
	 * @param $contextId int
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('status.tpl'));
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request) {
		$context = Request::getContext();
		$depositDao = DAORegistry::getDAO('DepositDAO');
		$networkStatus = $this->_plugin->getSetting($context->getId(), 'pln_accepting');
		$networkStatusMessage = $this->_plugin->getSetting($context->getId(), 'pln_accepting_message');
		$rangeInfo = PKPHandler::getRangeInfo($request, 'deposits');

		if (!$networkStatusMessage) {
			if ($networkStatus === true) {
				$networkStatusMessage = __('plugins.generic.pln.notifications.pln_accepting');
			} else {
				$networkStatusMessage = __('plugins.generic.pln.notifications.pln_not_accepting');
			}
		}
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign('deposits', $depositDao->getByJournalId($context->getId(),$rangeInfo));
		$templateMgr->assign('networkStatus', $networkStatus);
		$templateMgr->assign('networkStatusMessage', $networkStatusMessage);
		$templateMgr->assign('plnStatusDocs', $this->_plugin->getSetting($context->getId(), 'pln_status_docs'));

		return parent::fetch($request);
	}

}
