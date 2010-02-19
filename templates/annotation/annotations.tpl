{**
* lemmasXML.tpl
*
* Copyright (c) 2003-2009 John Willinsky
* Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
*
* Display comments on an article.
*
* $Id: comments.tpl,v 1.26 2009/05/26 01:31:17 mcrider Exp $
*}
<data>
  <div id="annotation_data" xmlns="http://www.w3.org/1999/xhtml">
    <dl id="annotation_list">
  {if isset($lemmas)}
  {foreach from=$lemmas item=lemma}
  <dt class="annotation_lemma_{$lemma->getLemmaId()}"><span class="annotation_lemma {$lemma->getLemmaType()}"><a href="#annotation_lemma_{$lemma->getLemmaId()}">&#187;</a></span> {$lemma->getLemmaText()|escape:"html"} |
    <a href="{url op="addNote" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}" target="_parent">Add note</a> |
    <a href="{url op="deleteLemma" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}" target="_parent">Delete Lemma</a></dt>
  {foreach from=$lemma->getLemmaNotes() item=note}
  <dd>{$note->getNoteText()} <br />
    <a href="{url op="editNote" path=$note->getArticleId()|to_array:$note->getGalleyId():$note->getLemmaId():$note->getNoteId()}" target="_parent">Edit Note</a> |
    <a href="{url op="deleteNote" path=$note->getArticleId()|to_array:$note->getGalleyId():$note->getLemmaId():$note->getNoteId()}" target="_parent">Delete Note</a>
  </dd>
  {/foreach}
  {/foreach}
  {/if}
</dl>
  </div>

<lemmas>
  {if isset($lemmas)}
  {foreach from=$lemmas item=lemma}
  <lemma id="annotation_lemma_{$lemma->getLemmaId()}" articleId="{$lemma->getArticleId()}"
         userId="{$lemma->getUserId()}" galleyId="{$lemma->getGalleyId()}"
         startContainer="{$lemma->getStartContainer()}"
         endContainer="{$lemma->getEndContainer()}"
         startOffset="{$lemma->getStartOffset()}"
         endOffset="{$lemma->getEndOffset()}"
         lemmaType ="{$lemma->getLemmaType()}">
    <lemmaText><![CDATA[{$lemma->getLemmaText()}]]></lemmaText>
  </lemma>
  {/foreach}
  {/if}
</lemmas>
</data>