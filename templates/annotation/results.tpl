{**
 * searchResults.tpl
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display article search results.
 *
 * $Id: searchResults.tpl,v 1.47 2009/05/26 19:17:18 asmecher Exp $
 *}
{strip}
{assign var="pageTitle" value="navigation.annotationSearch"}
{include file="common/header.tpl"}
{/strip}

<script type="text/javascript">
{literal}
<!--
function ensureKeyword() {
	if (document.search.query.value == '') {
		alert({/literal}'{translate|escape:"jsparam" key="search.noKeywordError"}'{literal});
		return false;
	}
	document.search.submit();
	return true;
}
// -->
{/literal}
</script>

<br/>

	<form name="revise" action="{url op="advanced"}" method="post">
		<input type="hidden" name="query" value="{$query|escape}"/>
		<input type="hidden" name="searchJournal" value="{$searchJournal|escape}"/>
		<input type="hidden" name="dateFromMonth" value="{$dateFromMonth|escape}"/>
		<input type="hidden" name="dateFromDay" value="{$dateFromDay|escape}"/>
		<input type="hidden" name="dateFromYear" value="{$dateFromYear|escape}"/>
		<input type="hidden" name="dateToMonth" value="{$dateToMonth|escape}"/>
		<input type="hidden" name="dateToDay" value="{$dateToDay|escape}"/>
		<input type="hidden" name="dateToYear" value="{$dateToYear|escape}"/>
	</form>
	<a href="javascript:document.revise.submit()" class="action">{translate key="search.reviseSearch"}</a><br />


  {if isset($lemmas[0])}
  <h2>Matching Lemmas</h2>
    <ul>
  {foreach from=$lemmas item=lemma}
    <li class="annotation_lemma_{$lemma->getLemmaId()}">
      <span class="annotation_lemma {$lemma->getLemmaType()}"><a href="#annotation_lemma_{$lemma->getLemmaId()}">&#187;</a></span> {$lemma->getLemmaText()|escape:"html"}
    </li>
  {/foreach}
    </ul>
  {/if}

  {if isset($notes[0])}
    <h2>Matching Notes</h2>
    <ul>
    {foreach from=$notes item=note}
    <li>{$note->getNoteText()}</li>
    {/foreach}
    </ul>
  {/if}

<div>The following instructions don't really apply.</div>
{translate key="search.syntaxInstructions"}

{include file="common/footer.tpl"}
