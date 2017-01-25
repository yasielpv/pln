<?php

/**
 * @file plugins/generic/pln/controllers/grid/PLNStatusGridRow.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PLNStatusGridRow
 * @ingroup controllers_grid_PLNStatusGridRow
 *
 * @brief Handle PLNStatus deposit grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class PLNStatusGridRow extends GridRow {
	/**
	 * Constructor
	 */
	function __construct() {
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

			// Create the "reset deposit" action
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
