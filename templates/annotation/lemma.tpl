{**
* lemmaXML.tpl
*
* Copyright (c) 2003-2009 John Willinsky
* Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
*
* Display comments on an article.
*
* $Id: comments.tpl,v 1.26 2009/05/26 01:31:17 mcrider Exp $
*}
<data>
<dl id="annotation_list" xmlns="http://www.w3.org/1999/xhtml">
  <dt class="annotation_lemma_{$lemma->getLemmaId()}"><span class="annotation_lemma {$lemma->getLemmaType()}">&#187;</span> {$lemma->getLemmaText()|escape:"html"} |
    <a href="{url op="addNote" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}"  target="_parent">Add note</a> |
    <a href="{url op="deleteLemma" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}"  target="_parent">Delete Lemma</a></dt>
  {foreach from=$lemma->getLemmaNotes() item=note}
  <dd>{$note->getNoteText()|escape:"html"} <br />
    <a href="{url op="editNote" path=$note->getNoteId()}" target="_parent">Edit Note</a> |
    <a href="{url op="deleteNote" path=$note->getNoteId()}" target="_parent">Delete Note</a>
  </dd>
  {/foreach}
</dl>

<lemma id="annotation_lemma_{$lemma->getLemmaId()}" articleId="{$lemma->getArticleId()}"
         userId="{$lemma->getUserId()}" galleyId="{$lemma->getGalleyId()}"
         startContainer="{$lemma->getStartContainer()}"
         endContainer="{$lemma->getEndContainer()}"
         startOffset="{$lemma->getStartOffset()}"
         endOffset="{$lemma->getEndOffset()}"
         lemmaType ="{$lemma->getLemmaType()}">
    <lemmaText><![CDATA[{$lemma->getLemmaText()|escape:"html"}]]></lemmaText>
  </lemma>
</data>