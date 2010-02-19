{**
* index.tpl
*
* Copyright (c) 2003-2009 John Willinsky
* Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
*
* User index.
*
* $Id: index.tpl,v 1.44 2009/10/27 19:10:04 asmecher Exp $
*}
{strip}
{assign var="pageTitle" value="user.userAnnotationsHome"}
{include file="common/header.tpl"}
{/strip}

<div id="myAnnotations">
  <h3>{translate key="user.myRecentAnnotations"}</h3>
    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="info tocArticle">
    {iterate from=userLemmas item=lemma}
    {assign var=article value=$lemma->getLemmaArticle()}
    {assign var=journal value=$lemma->getLemmaJournal()}
    {assign var=articlePath value=$article->getBestArticleId($currentJournal)}
    <tr>
      <td width="70%"><span class="annotation_lemma {$lemma->getLemmaType()}">&#187;</span> {$lemma->getLemmaText()|escape:"html"}<br />
          <span class="annotation_metadata">Created {$lemma->getDateCreated()}</span>
      </td>
      <td width="30%" align="right" valign="top">
        [<a href="{url journal=$journal->getPath() page="annotation" op="addNote" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}">Add note</a>]
        [<a href="{url journal=$journal->getPath() page="annotation" op="deleteLemma" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}">Delete Lemma</a>]
      </td>
    </tr>
    <tr>
      <td width="70%" class="tocTitle">In <a href="{url journal=$journal->getPath() page="article" op="view" path=$articlePath}">{$article->getLocalizedTitle()|strip_unsafe_html}</a></td>
      <td width="30%" class="tocGalleys" align="right" valign="top">
			{foreach from=$article->getLocalizedGalleys() item=galley name=galleyList}
            <a href="{url journal=$journal->getPath() page="article" op="view" path=$articlePath|to_array:$galley->getBestGalleyId($journal)}" class="file">{$galley->getGalleyLabel()|escape}</a>
            {/foreach}
      </td>
    </tr>
    <tr>
      <td width="70%" class="tocAuthors">
			{foreach from=$article->getAuthors() item=author name=authorList}
				{$author->getFullName()|escape}{if !$smarty.foreach.authorList.last},{/if}
			{/foreach}
      </td>
      <td width="30%" class="tocPages" align="right" valign="top">{$article->getPages()|escape}</td>
    </tr>
    {assign var=notes value=$lemma->getLemmaNotes()}
    {if count($notes) != 0}
    {foreach from=$notes item=note}
    <tr>
      <td width="70%" align="left" valign="top" style="padding-left: 4em;">{$note->getNoteText()} <br />
          <span class="annotation_metadata">Created {$note->getDateCreated()} {if $note->getDateCreated() != $note->getDateModified()} Modified {$note->getDateModified()} {/if}</span>
      </td>
      <td width="30%" align="right" valign="top">
        [<a href="{url journal=$journal->getPath() page="annotation" op="editNote" path=$note->getArticleId()|to_array:$note->getGalleyId():$note->getLemmaId():$note->getNoteId()}">Edit Note</a>]
        [<a href="{url journal=$journal->getPath() page="annotation" op="deleteNote" path=$note->getArticleId()|to_array:$note->getGalleyId():$note->getLemmaId():$note->getNoteId()}">Delete Note</a>]
      </td>
    </tr>
    <tr><td colspan="2" class="separator"></td></tr>
    {/foreach}
    {/if}
    <tr><td colspan="2"><div class="separator"></div></td></tr>
    {/iterate}
  </table>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="info tocArticle">
  <tr>
    <td align="left" width="50%">{page_info iterator=$userLemmas}</td>
	<td align="right" width="50%">{page_links anchor="userLemmas" iterator=$userLemmas name="userLemmas"}</td>
  </tr>
</table>
{include file="common/footer.tpl"}
