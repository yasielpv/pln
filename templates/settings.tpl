{**
 * plugins/generic/pln/templates/settingsForm.tpl
 *
 * Copyright (c) 2013-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * PLN plugin settings
 *
 *}
{strip}
	{assign var="pageTitle" value="plugins.generic.pln.settings_page"}
{/strip}
{if $prerequisitesMissing|@count > 0}
	<ul>
		{foreach from=$prerequisitesMissing item=message}
			<li><span class='pkp_form_error'>{$message}</span></li>
		{/foreach}
	</ul>
{/if}
<div id="plnSettings">
	<form class="pkp_form" id="plnSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
		{include file="controllers/notification/inPlaceNotification.tpl" notificationId="PLNSettingsFormNotification"}

		{fbvFormArea id="PLNSettingsFormArea"}
			{fbvFormSection title="plugins.generic.pln.settings.terms_of_use" list=true}
				{if $hasIssn}
					{foreach name=terms from=$terms_of_use key=term_name item=term_data}
						{if $terms_of_use_agreement[$term_name]} 
							{assign var="checked" value="checked"}
						{else}
							{assign var="checked" value=""}
						{/if}

						{fbvElement type="checkbox" name="terms_agreed[$term_name]" id="terms_agreed[$term_name]" value="1" checked=$checked label=$term_data.term}

						{*
						<p>{$term_data.term}</p>
						<input type="checkbox" name="terms_agreed[{$term_name|escape}]" id="terms_agreed[{$term_name|escape}]" value="1"{if } checked{/if}><label class="agree" for="terms_agreed[{$term_name|escape}]">{translate key="plugins.generic.pln.settings.terms_of_use_agree"}</label>
						{if !$smarty.foreach.terms.last }<div class="separator">&nbsp;</div>{/if}
						*}
						
					{/foreach}
				{else}
					<p>{translate key="plugins.generic.pln.notifications.issn_setting"}</p>
				{/if}
			{/fbvFormSection}

			{fbvFormSection title="plugins.generic.pln.settings.journal_uuid" list=true}
				<p>{translate key="plugins.generic.pln.settings.journal_uuid_help"}</p>
				<input type="text" id="journal_uuid" name="journal_uuid"  size="36" maxlength="36" class="textField" value="{$journal_uuid|escape}" disabled="disabled"/>
			{/fbvFormSection}

			{fbvFormSection title="plugins.generic.pln.settings.refresh" list=true}
				<p>{translate key="plugins.generic.pln.settings.refresh_help"}</p>
				<input type="submit" id="refresh" name="refresh" class="button" value="{translate key="plugins.generic.pln.settings.refresh"}"/>
			{/fbvFormSection}

			{*
			{capture assign="cancelUrl"}{plugin_url path="cancelSubmit"}{/capture}

			{fbvFormButtons id="quickSubmit" submitText="common.save" cancelUrl=$cancelUrl cancelUrlTarget="_self"}
			*}
			

			{fbvFormButtons}

			{*
			{fbvFormSection list=true}
				{fbvElement type="radio" id="displayItems-issue" name="displayItems" value="issue" checked=$displayItems|compare:"issue" label="plugins.generic.webfeed.settings.currentIssue"}
				{fbvElement type="radio" id="displayItems-recent" name="displayItems" value="recent" checked=$displayItems|compare:"recent" label="plugins.generic.webfeed.settings.recent"}
				{fbvElement type="text" id="recentItems" value=$recentItems label="plugins.generic.webfeed.settings.recentArticles" size=$fbvStyles.size.SMALL}
			{/fbvFormSection}
			*}
			
		{/fbvFormArea}

		{*
		<table class="data">
			<tr>
				<td class="label">
					{fieldLabel name="terms_of_use" key="plugins.generic.pln.settings.terms_of_use"}
				</td>
				<td class="value">
					<p>{translate key="plugins.generic.pln.settings.terms_of_use_help"}</p>
					{if $hasIssn}
						{foreach name=terms from=$terms_of_use key=term_name item=term_data}
							<p>{$term_data.term}</p>
							<input type="checkbox" name="terms_agreed[{$term_name|escape}]" id="terms_agreed[{$term_name|escape}]" value="1"{if $terms_of_use_agreement[$term_name]} checked{/if}><label class="agree" for="terms_agreed[{$term_name|escape}]">{translate key="plugins.generic.pln.settings.terms_of_use_agree"}</label>
							{if !$smarty.foreach.terms.last }<div class="separator">&nbsp;</div>{/if}
						{/foreach}
					{else}
						<p>{translate key="plugins.generic.pln.notifications.issn_setting"}</p>
					{/if}
				</td>
			</tr>

			<tr><td colspan="2"><div class="separator">&nbsp;</div></td></tr>

			<tr>
				<td class="label">{fieldLabel name="journal_uuid" key="plugins.generic.pln.settings.journal_uuid"}</td>
				<td class="value">
					<p>{translate key="plugins.generic.pln.settings.journal_uuid_help"}</p>
					<input type="text" id="journal_uuid" name="journal_uuid"  size="36" maxlength="36" class="textField" value="{$journal_uuid|escape}" disabled="disabled"/>
				</td>
			</tr>

			<tr><td colspan="2"><div class="separator">&nbsp;</div></td></tr>

			<tr>
				<td class="label">{fieldLabel name="terms_of_use" key="plugins.generic.pln.settings.refresh"}</td>
				<td class="value">
					<p>{translate key="plugins.generic.pln.settings.refresh_help"}</p>
					<input type="submit" id="refresh" name="refresh" class="button" value="{translate key="plugins.generic.pln.settings.refresh"}"/>
				</td>
			</tr>

			<tr><td colspan="2"><div class="separator">&nbsp;</div></td></tr>

			<tr>
				<td class="label">
				</td>
				<td class="value">
					<input type="button" class="button" value="{translate key="common.cancel"}" onclick="document.location.href = '{url|escape:"quotes" page="manager" op="plugins" path="generic" escape="false"}'" />
					<input type="submit" name="save" class="button defaultButton" value="{translate key="common.save"}" {if not $hasIssn}disabled="disabled"{/if}/>
				</td>
			</tr>

		</table>
		*}
		
	</form>
</div>