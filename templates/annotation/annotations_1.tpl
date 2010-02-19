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
<lemmas>
  {if isset($lemmas)}
  {foreach from=$lemmas item=lemma}
  <lemma id="{$lemma->getLemmaId()}" articleId="{$lemma->getArticleId()}"
         userId="{$lemma->getUserId()}" galleyId="{$lemma->getGalleyId()}"
         startContainer="{$lemma->getStartContainer()}"
         endContainer="{$lemma->getEndContainer()}"
         startOffset="{$lemma->getStartOffset()}"
         endOffset="{$lemma->getEndOffset()}"
         lemmaType ="{$lemma->getLemmaType()}">
    <lemmaText><![CDATA[{$lemma->getLemmaText()|escape:"html"}]]></lemmaText>
    <addNoteURL>{url op="addNote" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}</addNoteURL>
    <addNoteURL>{url op="deleteLemma" path=$lemma->getArticleId()|to_array:$lemma->getGalleyId():$lemma->getLemmaId()}</addNoteURL>
    <notes>
      {foreach from=$lemma->getLemmaNotes() item=note}
      <note id="{$note->getNoteId()}" lemmaId="{$note->getLemmaId}"
            galleyId="{$note->getGalleyId()}" articleId="{$note->getArticleId()}">
        <noteText><div xmlns="http://www.w3.org/1999/xhtml">{$note->getNoteText()|escape:"html"}</div></noteText>
        <editNoteURL><div xmlns="http://www.w3.org/1999/xhtml">{url op="editNote" path=$note->getNoteId()}</div></editNoteURL>
        <deleteNoteURL><div xmlns="http://www.w3.org/1999/xhtml">{url op="deleteNote" path=$note->getNoteId()}</div></deleteNoteURL>
      </note>
      {/foreach}
    </notes>
  </lemma>
  {/foreach}
  {/if}
</lemmas>
