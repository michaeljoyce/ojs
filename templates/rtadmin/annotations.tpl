{**
 * annotations.tpl
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * RT Annotations settings.
 *
 * $Id: addthis.tpl,v 1.1 2009/07/06 19:53:55 asmecher Exp $
 *}
{strip}
{assign var="pageTitle" value="rt.admin.annotations"}
{include file="common/header.tpl"}
{/strip}

<form method="post" action='{url op="saveConfigureAnnotations"}'>

<p>{translate key="rt.admin.annotations.description"}</p>

<div class="separator">&nbsp;</div>

<h3>{translate key="rt.admin.annotations.basic"}</h3>
<table width="100%" class="data">
	<tr valign="top">
		<td class="label" width="20%"></td>
		<td width="80%"><input type="checkbox" name="annotationsEnabled" id="annotationsEnabled" {if isset($annotationsEnabled) && $annotationsEnabled} checked="checked" {/if} /> <label for="annotationsEnabled">{translate key="rt.admin.annotations.enabled"}</label></td>
	</tr>
</table>

<p><input type="submit" value='{translate key="common.save"}' class="button defaultButton" /> 
<input type="button" value='{translate key="common.cancel"}' class="button" onclick="document.location.href='{url page=rtadmin escape=false}'" />
</p>

</form>

{include file="common/footer.tpl"}
