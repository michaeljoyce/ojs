{**
 * index.tpl
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * User index.
 *
 * $Id$
 *}
{strip}
{assign var="pageTitle" value="user.userHome"}
{include file="common/header.tpl"}
{/strip}

{if $isSiteAdmin}
	{assign var="hasRole" value=1}
	&#187; <a href="{url journal="index" page=$isSiteAdmin->getRolePath()}">{translate key=$isSiteAdmin->getRoleName()}</a>
	{call_hook name="Templates::User::Index::Site"}
{/if}

<div id="myJournals">
{if !$currentJournal}<h3>{translate key="user.myJournals"}</h3>{/if}

{foreach from=$userJournals item=journal}
	<div id="journal">
	{assign var="hasRole" value=1}
	{if !$currentJournal}<h4><a href="{url journal=$journal->getPath() page="user"}">{$journal->getLocalizedTitle()|escape}</a></h4>
	{else}<h3>{$journal->getLocalizedTitle()|escape}</h3>{/if}
	{assign var="journalId" value=$journal->getjournalId()}
	{assign var="journalPath" value=$journal->getPath()}
	<table width="100%" class="info">
		{if $isValid.JournalManager.$journalId}
			<tr>
				<td>&#187; <a href="{url journal=$journalPath page="manager"}">{translate key="user.role.manager"}</a></td>
				<td></td>
				<td></td>
				<td></td>
				<td align="right">{if $setupIncomplete.$journalId}[<a href="{url journal=$journalPath page="manager" op="setup" path="1"}">{translate key="manager.setup"}</a>]{/if}</td>
			</tr>
		{/if}
		{if $isValid.SubscriptionManager.$journalId}
			<tr>
				<td width="20%" colspan="5">&#187; <a href="{url journal=$journalPath page="subscriptionManager"}">{translate key="user.role.subscriptionManager"}</a></td>
			</tr>
		{/if}
		{if $isValid.Editor.$journalId || $isValid.SectionEditor.$journalId || $isValid.LayoutEditor.$journalId || $isValid.Copyeditor.$journalId || $isValid.Proofreader.$journalId}
			<tr><td class="separator" width="100%" colspan="5">&nbsp;</td></tr>
		{/if}
		{if $isValid.Editor.$journalId}
			<tr>
				{assign var="editorSubmissionsCount" value=$submissionsCount.Editor.$journalId}
				<td>&#187; <a href="{url journal=$journalPath page="editor"}">{translate key="user.role.editor"}</a></td>
				<td>{if $editorSubmissionsCount[0]}
						<a href="{url journal=$journalPath page="editor" op="submissions" path="submissionsUnassigned"}">{$editorSubmissionsCount[0]} {translate key="common.queue.short.submissionsUnassigned"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsUnassigned"}</span>{/if}
				</td>
				<td>{if $editorSubmissionsCount[1]}
						<a href="{url journal=$journalPath page="editor" op="submissions" path="submissionsInReview"}">{$editorSubmissionsCount[1]} {translate key="common.queue.short.submissionsInReview"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsInReview"}</span>{/if}
				</td>
				<td>{if $editorSubmissionsCount[2]}
						<a href="{url journal=$journalPath page="editor" op="submissions" path="submissionsInEditing"}">{$editorSubmissionsCount[2]} {translate key="common.queue.short.submissionsInEditing"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsInEditing"}</span>{/if}
				</td>
				<td align="right">[<a href="{url journal=$journalPath page="editor" op="createIssue"}">{translate key="editor.issues.createIssue"}</a>] [<a href="{url journal=$journalPath page="editor" op="notifyUsers"}">{translate key="editor.notifyUsers"}</a>]</td>
			</tr>
		{/if}
		{if $isValid.SectionEditor.$journalId}
			{assign var="sectionEditorSubmissionsCount" value=$submissionsCount.SectionEditor.$journalId}
			<tr>
				<td>&#187; <a href="{url journal=$journalPath page="sectionEditor"}">{translate key="user.role.sectionEditor"}</a></td>
				<td></td>
				<td>{if $sectionEditorSubmissionsCount[0]}
						<a href="{url journal=$journalPath page="sectionEditor" op="index" path="submissionsInReview"}">{$sectionEditorSubmissionsCount[0]} {translate key="common.queue.short.submissionsInReview"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsInReview"}</span>{/if}
				</td>
				<td>{if $sectionEditorSubmissionsCount[1]}
						<a href="{url journal=$journalPath page="sectionEditor" op="index" path="submissionsInEditing"}">{$sectionEditorSubmissionsCount[1]} {translate key="common.queue.short.submissionsInEditing"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsInEditing"}</span>{/if}
				</td>
				<td align="right"></td>
			</tr>
		{/if}
		{if $isValid.LayoutEditor.$journalId}
			{assign var="layoutEditorSubmissionsCount" value=$submissionsCount.LayoutEditor.$journalId}
			<tr>
				<td>&#187; <a href="{url journal=$journalPath page="layoutEditor"}">{translate key="user.role.layoutEditor"}</a></td>
				<td></td>
				<td></td>
				<td>{if $layoutEditorSubmissionsCount[0]}
						<a href="{url journal=$journalPath page="layoutEditor" op="submissions"}">{$layoutEditorSubmissionsCount[0]} {translate key="common.queue.short.submissionsInEditing"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsInEditing"}</span>{/if}
				</td>
				<td align="right"></td>
			</tr>
		{/if}
		{if $isValid.Copyeditor.$journalId}
			{assign var="copyeditorSubmissionsCount" value=$submissionsCount.Copyeditor.$journalId}
			<tr>
				<td>&#187; <a href="{url journal=$journalPath page="copyeditor"}">{translate key="user.role.copyeditor"}</a></td>
				<td></td>
				<td></td>
				<td>{if $copyeditorSubmissionsCount[0]}
						<a href="{url journal=$journalPath page="copyeditor"}">{$copyeditorSubmissionsCount[0]} {translate key="common.queue.short.submissionsInEditing"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsInEditing"}</span>{/if}
				</td>
				<td align="right"></td>
			</tr>
		{/if}
		{if $isValid.Proofreader.$journalId}
			{assign var="proofreaderSubmissionsCount" value=$submissionsCount.Proofreader.$journalId}
			<tr>
				<td>&#187; <a href="{url journal=$journalPath page="proofreader"}">{translate key="user.role.proofreader"}</a></td>
				<td></td>
				<td></td>
				<td>{if $proofreaderSubmissionsCount[0]}
						<a href="{url journal=$journalPath page="proofreader"}">{$proofreaderSubmissionsCount[0]} {translate key="common.queue.short.submissionsInEditing"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.submissionsInEditing"}</span>{/if}
				</td>
				<td align="right"></td>
			</tr>
		{/if}
		{if $isValid.Author.$journalId || $isValid.Reviewer.$journalId}
			<tr><td class="separator" width="100%" colspan="5">&nbsp;</td></tr>
		{/if}
		{if $isValid.Author.$journalId}
			{assign var="authorSubmissionsCount" value=$submissionsCount.Author.$journalId}
			<tr>
				<td>&#187; <a href="{url journal=$journalPath page="author"}">{translate key="user.role.author"}</a></td>
				<td></td>
				<td></td>
				<td>{if $authorSubmissionsCount[0]}
						<a href="{url journal=$journalPath page="author"}">{$authorSubmissionsCount[0]} {translate key="common.queue.short.active"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.active"}</span>{/if}
				</td>
				<td align="right">[<a href="{url journal=$journalPath page="author" op="submit"}">{translate key="author.submit"}</a>]</td>
			</tr>
		{/if}
		{if $isValid.Reviewer.$journalId}
			{assign var="reviewerSubmissionsCount" value=$submissionsCount.Reviewer.$journalId}
			<tr>
				<td>&#187; <a href="{url journal=$journalPath page="reviewer"}">{translate key="user.role.reviewer"}</a></td>
				<td></td>
				<td></td>
				<td>{if $reviewerSubmissionsCount[0]}
						<a href="{url journal=$journalPath page="reviewer"}">{$reviewerSubmissionsCount[0]} {translate key="common.queue.short.active"}</a>
					{else}<span class="disabled">0 {translate key="common.queue.short.active"}</span>{/if}
				</td>
				<td align="right"></td>
			</tr>
		{/if}
		{* Add a row to the bottom of each table to ensure all have same width*}
		<tr>
			<td width="25%"></td>
			<td width="12%"></td>
			<td width="12%"></td>
			<td width="12%"></td>
			<td width="39%"></td>
		</tr>
			
	</table>
	{call_hook name="Templates::User::Index::Journal" journal=$journal}
	</div>
{/foreach}
</div>	


{if !$hasRole}
	{if $currentJournal}
		<div id="noRolesForJournal">
		<p>{translate key="user.noRoles.noRolesForJournal"}</p>
		<ul class="plain">
			<li>
				&#187;
				{if $allowRegAuthor}
					{url|assign:"submitUrl" page="author" op="submit"}
					<a href="{url op="become" path="author" source=$submitUrl}">{translate key="user.noRoles.submitArticle"}</a>
				{else}{* $allowRegAuthor *}
					{translate key="user.noRoles.submitArticleRegClosed"}
				{/if}{* $allowRegAuthor *}
			</li>
			<li>
				&#187;
				{if $allowRegReviewer}
					{url|assign:"userHomeUrl" page="user" op="index"}
					<a href="{url op="become" path="reviewer" source=$userHomeUrl}">{translate key="user.noRoles.regReviewer"}</a>
				{else}{* $allowRegReviewer *}
					{translate key="user.noRoles.regReviewerClosed"}
				{/if}{* $allowRegReviewer *}
			</li>
		</ul>
		</div>
	{else}{* $currentJournal *}
		<div id="currentJournal">
		<p>{translate key="user.noRoles.chooseJournal"}</p>
		<ul class="plain">
			{foreach from=$allJournals item=thisJournal}
				<li>&#187; <a href="{url journal=$thisJournal->getPath() page="user" op="index"}">{$thisJournal->getLocalizedTitle()|escape}</a></li>
			{/foreach}
		</ul>
		</div>
	{/if}{* $currentJournal *}
{/if}{* !$hasRole *}

<div id="myAccount">
<h3>{translate key="user.myAccount"}</h3>
<ul class="plain">
	{if $hasOtherJournals}
		{if !$showAllJournals}
			<li>&#187; <a href="{url journal="index" page="user"}">{translate key="user.showAllJournals"}</a></li>
		{/if}
	{/if}
	{if $currentJournal}
		{if $subscriptionsEnabled}
			<li>&#187; <a href="{url page="user" op="subscriptions"}">{translate key="user.manageMySubscriptions"}</a></li>
		{/if}
	{/if}
	<li>&#187; <a href="{url page="user" op="profile"}">{translate key="user.editMyProfile"}</a></li>

	{if !$implicitAuth}
		<li>&#187; <a href="{url page="user" op="changePassword"}">{translate key="user.changeMyPassword"}</a></li>
	{/if}

	{if $currentJournal}
		{if $journalPaymentsEnabled && $membershipEnabled}
			{if $dateEndMembership}
				<li>&#187; <a href="{url page="user" op="payMembership"}">{translate key="payment.membership.renewMembership"}</a> ({translate key="payment.membership.ends"}: {$dateEndMembership|date_format:$dateFormatShort})</li>
			{else}
				<li>&#187; <a href="{url page="user" op="payMembership"}">{translate key="payment.membership.buyMembership"}</a></li>		
			{/if}
		{/if}{* $journalPaymentsEnabled && $membershipEnabled *}
	{/if}{* $userJournal *}

	<li>&#187; <a href="{url page="login" op="signOut"}">{translate key="user.logOut"}</a></li>
	{call_hook name="Templates::User::Index::MyAccount"}
</ul>
</div>

<div id="myAnnotations">
  <h3>{translate key="user.myRecentAnnotations"}</h3>
  <p><a href="{url page="annotation"}">{translate key="user.allAnnotationsLink"}</a></p>
    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="info tocArticle">
    {foreach from=$userLemmas item=lemma}
    {assign var=article value=$lemma->getLemmaArticle()}
    {assign var=journal value=$lemma->getLemmaJournal()}
    {assign var=articlePath value=$article->getBestArticleId($currentJournal)}
    <tr>
      <td width="70%"><span class="annotation_lemma {$lemma->getLemmaType()}">&#187;</span> {$lemma->getLemmaText()|escape:"html"}</td>
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
      <td width="70%" align="left" valign="top" style="padding-left: 4em;">{$note->getNoteText()}</td>
      <td width="30%" align="right" valign="top">
        [<a href="{url journal=$journal->getPath() page="annotation" op="editNote" path=$note->getArticleId()|to_array:$note->getGalleyId():$note->getLemmaId():$note->getNoteId()}">Edit Note</a>]
        [<a href="{url journal=$journal->getPath() page="annotation" op="deleteNote" path=$note->getArticleId()|to_array:$note->getGalleyId():$note->getLemmaId():$note->getNoteId()}">Delete Note</a>]
      </td>
    </tr>
    <tr><td colspan="2" class="separator"></td></tr>
    {/foreach}
    {/if}
    <tr><td colspan="2"><div class="separator"></div></td></tr>
    {/foreach}
  </table>
</div>

{include file="common/footer.tpl"}
