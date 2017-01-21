<?php

/**
 * @file plugins/generic/pln/controllers/grid/PLNStatusGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2000-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PLNStatusGridCellProvider
 * @ingroup controllers_grid_PLNStatusGridCellProvider
 *
 * @brief Class for a cell provider to display information about PLN Deposits
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');
import('lib.pkp.classes.linkAction.request.RedirectAction');

class PLNStatusGridCellProvider extends GridCellProvider {
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}


	//
	// Template methods from GridCellProvider
	//
	///**
	// * Get cell actions associated with this row/column combination
	// * @param $row GridRow
	// * @param $column GridColumn
	// * @return array an array of LinkAction instances
	// */
	//function getCellActions($request, $row, $column, $position = GRID_ACTION_POSITION_DEFAULT) {
	//    $staticPage = $row->getData();

	//    switch ($column->getId()) {
	//        case 'path':
	//            $dispatcher = $request->getDispatcher();
	//            return array(new LinkAction(
	//                'details',
	//                new RedirectAction(
	//                    $dispatcher->url($request, ROUTE_PAGE, null) . '/' . $staticPage->getComplete(),
	//                    'staticPage'
	//                ),
	//                $staticPage->getComplete()
	//            ));
	//        default:
	//            return parent::getCellActions($request, $row, $column, $position);
	//    }
	//}

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$deposit = $row->getData();

		switch ($column->getId()) {
			case 'id':
				// The action has the label
				return array('label' => $deposit->getId());
			case 'type':
				return array('label' => $deposit->getObjectType());
			case 'checked':
				return array('label' => $deposit->getStatus());
			case 'local_status':
				return array('label' => $deposit->getLocalStatus());
			case 'processing_status':
				return array('label' => $deposit->getProcessingStatus());
			case 'lockss_status':
				return array('label' => $deposit->getLockssStatus());
			case 'complete':
				return array('label' => $deposit->getComplete());
		}
	}
}

?>
