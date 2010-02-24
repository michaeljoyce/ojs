{**
 * header.tpl
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Article View -- Header component.
 *
 * $Id$
 *}
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$article->getLocalizedTitle()|escape} | {$article->getFirstAuthor(true)|escape} | {$currentJournal->getLocalizedTitle()|escape}</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$defaultCharset|escape}" />
	<meta name="description" content="{$article->getLocalizedTitle()|escape}" />
	{if $article->getLocalizedSubject()}
		<meta name="keywords" content="{$article->getLocalizedSubject()|escape}" />
	{/if}

	{include file="article/dublincore.tpl"}
	{include file="article/googlescholar.tpl"}

	<link rel="stylesheet" href="{$baseUrl}/lib/pkp/styles/common.css" type="text/css" />
	<link rel="stylesheet" href="{$baseUrl}/styles/common.css" type="text/css" />
	<link rel="stylesheet" href="{$baseUrl}/styles/articleView.css" type="text/css" />

	{foreach from=$stylesheets item=cssUrl}
		<link rel="stylesheet" href="{$cssUrl}" type="text/css" />
	{/foreach}

	<script type="text/javascript" src="{$baseUrl}/lib/pkp/js/general.js"></script>
	{$additionalHeadData}
	
	{if $annotationsEnabled && $isUserLoggedIn}
	  <!-- //MSJ the annotations header stuff is here. -->
          <script type="text/javascript">
              {* Smarty tries to parse out the curly braces, but JSON syntax requires them to be present, hence the <literal> tags. *}
              var annotations_config = {literal} { {/literal}
                  galley_id : '{$galleyId}',
                  getLemmas : '{url page="annotation" op="view"}/{$articleId}',
                  saveLemma : '{url page="annotation" op="saveLemma"}/{$articleId}',
              {literal} } {/literal}
          </script>
          <script type="text/javascript" src="{$baseUrl}/lib/pkp/lib/ierange/ierange-m2.js"></script>
          <script type="text/javascript" src="{$baseUrl}/lib/pkp/lib/jquery/jquery-1.3.1.min.js"></script>
	  <script type="text/javascript" src="{$baseUrl}/lib/pkp/js/annotations/lemma.js"></script>
	  <script type="text/javascript" src="{$baseUrl}/lib/pkp/js/annotations/highlight.js"></script>
	  <script type="text/javascript" src="{$baseUrl}/lib/pkp/js/annotations/bookmark.js"></script>
	  <script type="text/javascript" src="{$baseUrl}/lib/pkp/js/annotations/lib.js"></script>
	  <script type="text/javascript" src="{$baseUrl}/lib/pkp/js/annotations/control.js"></script>
	{/if}
    <link rel="stylesheet" href="{$baseUrl}/lib/pkp/styles/annotations.css" type="text/css" />

</head>
<body>

<div id="container">

<div id="body">

<div id="main">

<h2>{$siteTitle|escape}{if $issue},&nbsp;{$issue->getIssueIdentification(false,true)|escape}{/if}</h2>

<div id="navbar">
	<ul class="menu">
		<li><a href="{url page="index"}" target="_parent">{translate key="navigation.home"}</a></li>
		<li><a href="{url page="about"}" target="_parent">{translate key="navigation.about"}</a></li>

		{if $isUserLoggedIn}
			<li><a href="{url journal="index" page="user"}" target="_parent">{translate key="navigation.userHome"}</a></li>
		{else}
			<li><a href="{url page="login"}" target="_parent">{translate key="navigation.login"}</a></li>
			<li><a href="{url page="user" op="register"}" target="_parent">{translate key="navigation.register"}</a></li>
		{/if}{* $isUserLoggedIn *}

		{if !$currentJournal || $currentJournal->getSetting('publishingMode') != $smarty.const.PUBLISHING_MODE_NONE}
			<li><a href="{url page="search"}" target="_parent">{translate key="navigation.search"}</a></li>
		{/if}

		{if $currentJournal && $currentJournal->getSetting('publishingMode') != $smarty.const.PUBLISHING_MODE_NONE}
			<li><a href="{url page="issue" op="current"}" target="_parent">{translate key="navigation.current"}</a></li>
			<li><a href="{url page="issue" op="archive"}" target="_parent">{translate key="navigation.archives"}</a></li>
		{/if}

		{if $enableAnnouncements}
			<li><a href="{url page="announcement"}" target="_parent">{translate key="announcement.announcements"}</a></li>
		{/if}{* $enableAnnouncements *}

		{call_hook name="Templates::Common::Header::Navbar::CurrentJournal"}

		{foreach from=$navMenuItems item=navItem}
			{if $navItem.url != '' && $navItem.name != ''}
				<li><a href="{if $navItem.isAbsolute}{$navItem.url|escape}{else}{url page=$requestedPage}{$navItem.url|escape}{/if}" target="_parent">{if $navItem.isLiteral}{$navItem.name|escape}{else}{translate key=$navItem.name}{/if}</a></li>
			{/if}
		{/foreach}
	</ul>
</div>
<div id="breadcrumb">
	<a href="{url page="index"}" target="_parent">{translate key="navigation.home"}</a> &gt;
	{if $issue}<a href="{url page="issue" op="view" path=$issue->getBestIssueId($currentJournal)}" target="_parent">{$issue->getIssueIdentification(false,true)|escape}</a> &gt;{/if}
	<a href="{url page="article" op="view" path=$articleId|to_array:$galleyId}" class="current" target="_parent">{$article->getFirstAuthor(true)|escape}</a>
</div>

{if $annotationsEnabled}
  {if $isUserLoggedIn}
	<div class="annotations annotationsControls">
	{translate key="rt.annotations"}
	  <ul id="annotations_toggles">
		<li>{translate key="rt.annotations.highlights"}: <a id="annotations_highlight_toggle_off" class="annotation_control">{translate key="common.off"}</a></li>
		<li>{translate key="rt.annotations.highlights"}: <a id="annotations_highlight_toggle_on" class="annotation_control">{translate key="common.on"}</a></li>
		<li>{translate key="rt.annotations.notes"}: <a id="annotations_notes_toggle_off" class="annotation_control">{translate key="common.off"}</a></li>
		<li>{translate key="rt.annotations.notes"}: <a id="annotations_notes_toggle_on" class="annotation_control">{translate key="common.on"}</a></li>
	  </ul>
	  <ul id="annotation_triggers">
		<li>{translate key="rt.annotations.highlight"}:
                    <a class="annotation_control" href="#" onclick="newUserLemma('highlight_red'); return false;">{translate key="common.color.red"}</a> |
                    <a class="annotation_control" href="#" onclick="newUserLemma('highlight_green'); return false;">{translate key="common.color.green"}</a> |
                    <a class="annotation_control" href="#" onclick="newUserLemma('highlight_blue'); return false;">{translate key="common.color.blue"}</a> |
                    <a class="annotation_control" href="#" onclick="newUserLemma('highlight_cyan'); return false;">{translate key="common.color.cyan"}</a> |
                    <a class="annotation_control" href="#" onclick="newUserLemma('highlight_magenta'); return false;">{translate key="common.color.magenta"}</a> |
                    <a class="annotation_control" href="#" onclick="newUserLemma('highlight_yellow'); return false;">{translate key="common.color.yellow"}</a>
                </li>
                <li id="annotations_bookmark_start_ctl" style="display:list-item;">{translate key="rt.annotations.bookmark"}: <a id="annotation_bookmark_trigger_start" class="annotation_control" href="#" onclick="startBookmark('bookmark'); return false;">{translate key="common.create"}</a></li>
                <li id="annotations_bookmark_stop_ctl" style="display: none;">{translate key="rt.annotations.bookmark"}: {translate key="rt.annotations.bookmark.directions"} <a id="annotation_bookmark_trigger_stop" class="annotation_control" href="#" onclick="stopBookmark('bookmark'); return false;">{translate key="common.cancel"}</a></li>
	  </ul>
	</div>
  {else}
	<div class="annotations annotationsControls">
      <p>{translate key="annotations.mustLogin"}  [<a href="{url page="login"}" target="_parent">{translate key="navigation.login"}</a>]</p>
    </div>
  {/if}

  <div id="annotation_content"></div>
{/if}

<div id="content">
