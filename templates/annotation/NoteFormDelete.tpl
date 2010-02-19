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
{assign var="pageTitle" value="annotations.confirmDelete"}
{include file="common/header.tpl"}
{/strip}

{include file="common/formErrors.tpl"}
<div id="noteForm">
<form name="submit" action="{if isset($noteId)}{url op="deleteNote" path=$articleId|to_array:$galleyId:$lemmaId:$noteId:"delete"}{/if}" method="post">
      <p>{translate key="annotations.confirmDeleteQuestion"}</p>
<table class="data" width="100%">
  <tr valign="top">
    <td class="label" width="20%">{translate key="annotations.lemma"}</td>
    <td class="value" width="80%">{$lemma->getLemmaText()}</td>
  </tr>
	<tr valign="top">
		<td class="label" width="20%">{translate key="annotations.noteContent"}</td>
		<td class="value" width="80%">{$note->getNoteText()}</td>
	</tr>
</table>
<p><input type="submit" value="{translate key="common.delete"}" class="button defaultButton" />
   <input type="button" value="{translate key="common.cancel"}" class="button" onclick="location.href='{url page="article" op="view" path=$articleId|to_array:$galleyId:$parentId}';" />
</p>

</form>
</div>

{include file="common/footer.tpl"}
