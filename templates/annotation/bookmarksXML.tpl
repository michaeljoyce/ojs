{**
* bookmarksXML.tpl
*
* Copyright (c) 2003-2009 John Willinsky
* Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
*
* Display comments on an article.
*
* $Id: comments.tpl,v 1.26 2009/05/26 01:31:17 mcrider Exp $
*}
<bookmarks>
    {if isset($bookmarks)}
    {foreach from=$bookmarks item=bookmark}
    <bookmark id="{$bookmark->getBookmarkId()}"
              articleId="{$bookmark->getArticleId()}"
              userId="{$bookmark->getUserId()}" galleyId="{$bookmark->getGalleyId()}"
              container="{$bookmark->getContainer()}"
              type ="{$bookmark->getBookmarkType()}"
              description ="{$bookmark->getDescription()|escape:"html"}" />
              {/foreach}
              {/if}
</bookmarks>
