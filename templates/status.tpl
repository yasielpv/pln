{**
 * plugins/generic/pln/templates/settingsForm.tpl
 *
 * Copyright (c) 2013-2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * PLN plugin settings
 *
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#plnStatusForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

{strip}
{assign var="pageTitle" value="plugins.generic.pln.status_page"}
{/strip}

{capture assign="confirmReset"}{translate key="plugins.generic.pln.status.confirmReset"}{/capture}
<div id="plnStatus">
	<h3>{translate key="plugins.generic.pln.status.deposits"}</h3>
	<p>{translate key="plugins.generic.pln.status.network_status" networkStatusMessage=$networkStatusMessage}</p>
	<form class="pkp_form" id="plnStatusForm" method="post" action="{plugin_url path="status"}">
		{capture assign="depositsGridUrl"}{url component="plugins.generic.pln.controllers.grid.PLNStatusGridHandler" op="fetchGrid" escape=false}{/capture}
		{load_url_in_div id="statusDepositsGridContainer" url=$depositsGridUrl}
	</form>
	<p>{translate key='plugins.generic.pln.status.docs' statusDocsUrl=$plnStatusDocs}</p>
</div>
