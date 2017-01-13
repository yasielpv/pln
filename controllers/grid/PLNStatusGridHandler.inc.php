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
	function PLNStatusGridHandler() {
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('fetchGrid', 'fetchRow', 'addCustomBlock', 'editCustomBlock', 'updateCustomBlock', 'resetDeposit')
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

	/**
	 * Delete a custom block
	 * @param $args array
	 * @param $request PKPRequest
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

	//
	// Public Grid Actions
	//
	/**
	 * An action to add a new custom block
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 */
	function addCustomBlock($args, $request) {
		// Calling editCustomBlock with an empty ID will add
		// a new custom block.
		return $this->editCustomBlock($args, $request);
	}

	/**
	 * An action to edit a custom block
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 * @return string Serialized JSON object
	 */
	function editCustomBlock($args, $request) {
		$blockName = $request->getUserVar('blockName');
		$context = $request->getContext();
		$this->setupTemplate($request);

		$customBlockPlugin = null;
		// If this is the edit of the existing custom block plugin,
		if ($blockName) {
			// Create the custom block plugin
			import('plugins.generic.customBlockManager.CustomBlockPlugin');
			$customBlockPlugin = new CustomBlockPlugin($blockName, CUSTOMBLOCKMANAGER_PLUGIN_NAME);
		}

		// Create and present the edit form
		import('plugins.generic.customBlockManager.controllers.grid.form.CustomBlockForm');
		$customBlockManagerPlugin = $this->plugin;
		$template = $customBlockManagerPlugin->getTemplatePath() . 'editCustomBlockForm.tpl';
		$customBlockForm = new CustomBlockForm($template, $context->getId(), $customBlockPlugin);
		$customBlockForm->initData();
		$json = new JSONMessage(true, $customBlockForm->fetch($request));
		return $json->getString();
	}

	/**
	 * Update a custom block
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function updateCustomBlock($args, $request) {
		$pluginName = $request->getUserVar('existingBlockName');
		$context = $request->getContext();
		$this->setupTemplate($request);

		$customBlockPlugin = null;
		// If this was the edit of the existing custom block plugin
		if ($pluginName) {
			// Create the custom block plugin
			import('plugins.generic.customBlockManager.CustomBlockPlugin');
			$customBlockPlugin = new CustomBlockPlugin($pluginName, CUSTOMBLOCKMANAGER_PLUGIN_NAME);
		}

		// Create and populate the form
		import('plugins.generic.customBlockManager.controllers.grid.form.CustomBlockForm');
		$customBlockManagerPlugin = $this->plugin;
		$template = $customBlockManagerPlugin->getTemplatePath() . 'editCustomBlockForm.tpl';
		$customBlockForm = new CustomBlockForm($template, $context->getId(), $customBlockPlugin);
		$customBlockForm->readInputData();

		// Check the results
		if ($customBlockForm->validate()) {
			// Save the results
			$customBlockForm->execute();
 			return DAO::getDataChangedEvent();
		} else {
			// Present any errors
			$json = new JSONMessage(true, $customBlockForm->fetch($request));
			return $json->getString();
		}
	}

	/**
	 * Delete a custom block
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function deleteCustomBlock($args, $request) {
		$blockName = $request->getUserVar('blockName');
		$context = $request->getContext();

		// Delete all the entries for this block plugin
		$pluginSettingsDao = DAORegistry::getDAO('PluginSettingsDAO');
		$pluginSettingsDao->deleteSetting($context->getId(), $blockName, 'enabled');
		$pluginSettingsDao->deleteSetting($context->getId(), $blockName, 'context');
		$pluginSettingsDao->deleteSetting($context->getId(), $blockName, 'seq');
		$pluginSettingsDao->deleteSetting($context->getId(), $blockName, 'blockContent');

		// Remove this block plugin from the list of the custom block plugins
		$customBlockManagerPlugin = $this->plugin;
		$blocks = $customBlockManagerPlugin->getSetting($context->getId(), 'blocks');
		$newBlocks = array_diff($blocks, array($blockName));
		ksort($newBlocks);
		$customBlockManagerPlugin->updateSetting($context->getId(), 'blocks', $newBlocks);
		return DAO::getDataChangedEvent();
	}
}

?>
