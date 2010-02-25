{**
 * comment.tpl
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Article reader comment editing
 *
 * $Id: comment.tpl,v 1.21 2009/05/26 01:31:17 mcrider Exp $
 *}
{strip}
{assign var="pageTitle" value="annotations.enterNote"}
{include file="common/header.tpl"}
{/strip}

{include file="common/formErrors.tpl"}
<div id="noteForm">
<form name="submit" action="{if isset($noteId)}{url op="editNote" path=$articleId|to_array:$galleyId:$lemmaId:$noteId:"save"}{else}{url op="addNote" path=$articleId|to_array:$galleyId:$lemmaId:"save"}{/if}" method="post">
<table class="data" width="100%">
  <tr valign="top">
    <td class="label" width="20%">{translate key="rt.annotations.lemma"}</td>
    <td class="value" width="80%">{$lemma->getLemmaText()}</td>
  </tr>
	<tr valign="top">
		<td class="label" width="20%"><label for="noteContent">{translate key="rt.annotations.noteContent"}</label></td>
		<td class="value" width="80%">
			<textarea class="textArea" name="noteText" id="noteText" rows="5" cols="60">{if isset($note)}{$note->getNoteText()}{/if}</textarea>
		</td>
	</tr>

</table>
<p><input type="submit" value="{translate key="common.save"}" class="button defaultButton" /> 
   <input type="button" value="{translate key="common.cancel"}" class="button" onclick="location.href='{url page="article" op="view" path=$articleId|to_array:$galleyId:$parentId}';" />
</p>

</form>
</div>

{include file="common/footer.tpl"}
