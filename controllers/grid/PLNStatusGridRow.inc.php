<?php

/**
 * @file plugins/generic/customBlockManager/controllers/grid/CustomBlockGridRow.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomBlockGridRow
 * @ingroup controllers_grid_customBlockManager
 *
 * @brief Handle custom blocks grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class PLNStatusGridRow extends GridRow {
	/**
	 * Constructor
	 */
	function PLNStatusGridRow() {
		parent::__construct();
	}

	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request) {
		parent::initialize($request);

		$rowId = $this->getId();
        $actionArgs['DepositId'] = $rowId;
		if (!empty($rowId)) {
			$router = $request->getRouter();

			// Create the "delete custom block" action
			import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
			$this->addAction(
				new LinkAction(
					'resetDeposit',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('plugins.generic.pln.status.confirmReset'),
						__('common.reset'),

						$router->url($request, null, null, 'resetDeposit', null, $actionArgs, 'modal_reset')
					),
					__('common.reset'),
					'reset'
				)
			);
		}
	}
}

?>
