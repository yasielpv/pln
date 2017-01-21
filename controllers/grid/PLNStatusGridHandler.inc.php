<?php

/**
 * @file plugins/generic/pln/controllers/grid/PLNStatusGridHandler.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PLNStatusGridHandler
 *
 * @brief Handle PLNStatus grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.pln.controllers.grid.PLNStatusGridRow');
import('plugins.generic.pln.controllers.grid.PLNStatusGridCellProvider');

class PLNStatusGridHandler extends GridHandler {
	/** @var PLNPlugin The pln plugin */
	static $plugin;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('fetchGrid', 'fetchRow', 'resetDeposit')
		);
		$this->plugin = PluginRegistry::getPlugin('generic', PLN_PLUGIN_NAME);
	}

	/**
	 * Set the translator plugin.
	 * @param $plugin StaticPagesPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}


	//
	// Overridden template methods
	//
	/**
	 * @copydoc Gridhandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request);

		// Set the grid title.
		$this->setTitle('plugins.generic.pln.status.deposits');

		// Set the grid instructions.
		$this->setEmptyRowText('common.none');

		// Columns
		$cellProvider = new PLNStatusGridCellProvider();
		$this->addColumn(new GridColumn(
			'id',
			'common.id',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'type',
			'common.type',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'checked',
			'plugins.generic.pln.status.checked',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'local_status',
			'plugins.generic.pln.status.local_status',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'processing_status',
			'plugins.generic.pln.status.processing_status',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'lockss_status',
			'plugins.generic.pln.status.lockss_status',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'complete',
			'plugins.generic.pln.status.complete',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
	}

	/**
	 * @copydoc GridHandler::initFeatures()
	 */
	function initFeatures($request, $args) {
		import('lib.pkp.classes.controllers.grid.feature.PagingFeature');
		return array(new PagingFeature());
	}

	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	protected function getRowInstance() {
		import('plugins.generic.pln.controllers.grid.PLNStatusGridRow');
		return new PLNStatusGridRow();
	}

	/**
	 * @copydoc GridHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.ContextAccessPolicy');
		$this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments, false);
	}

	/**
	 * @copydoc GridHandler::loadData()
	 */
	protected function loadData($request, $filter) {
		$context = $request->getContext();
		$depositDao = DAORegistry::getDAO('DepositDAO');
		$rangeInfo = $this->getGridRangeInfo($request, $this->getId());
		return $depositDao->getDepositsByJournalId($context->getId(), $rangeInfo);
	}

	//
	// Public Grid Actions
	//
	/**
	 * Reset Deposit
	 * @param $args array
	 * @param $request PKPRequest
	 *
	 * @return string Serialized JSON object
	 */
	function resetDeposit($args, $request) {
		$context = $request->getContext();
		$deposit_id = $args['DepositId'];
		$depositDao = DAORegistry::getDAO('DepositDAO');

		if (!is_null($deposit_id)) {
			$deposit = $depositDao->getDepositById($context->getId(), $deposit_id);
			$deposit->setStatus(PLN_PLUGIN_DEPOSIT_STATUS_NEW);
			$depositDao->updateObject($deposit);
		}

		return DAO::getDataChangedEvent();
	}
}

?>
