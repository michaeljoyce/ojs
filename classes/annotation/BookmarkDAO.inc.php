<?php

import('annotation.Bookmark');
class BookmarkDAO extends DAO {

  function insertBookmark($bookmark) {
    $result = false;
    $result = $this->update('INSERT INTO annotation_bookmarks (user_id, article_id, galley_id, container, bookmark_type, bookmark_desc) VALUES
        (?, ?, ?, ?, ?, ?)',
        array($bookmark->getUserId(),
        $bookmark->getArticleId(),
        $bookmark->getGalleyId(),
        $bookmark->getContainer(),
        $bookmark->getBookmarkType(),
        $bookmark->getDescription(),
        )
    );
    return $result;
  }

  function getBookmarksByArticle($articleId, $userId) {
    $bookmarks = array();
    $result =& $this->retrieve("SELECT * FROM annotation_bookmarks WHERE article_id = ? AND user_id = ?",
        array((int) $articleId, (int) $userId));
    while( ! $result->EOF) {
      $bookmarks[] = $this->_returnBookmarkFromRow($result->GetRowAssoc(false));
      $result->MoveNext();
    }
    $result->Close();
    unset($result);
    return $bookmarks;
  }

  function _returnBookmarkFromRow($row) {
    $bookmark = new bookmark();
    $bookmark->setArticleId($row['article_id']);
    $bookmark->setBookmarkId($row['bookmark_id']);
    $bookmark->setUserId($row['user_id']);
    $bookmark->setGalleyId($row['galley_id']);
    $bookmark->setContainer($row['container']);
    $bookmark->setBookmarkType($row['bookmark_type']);
    $bookmark->setDescription($row['bookmark_desc']);

    //HookRegistry::call('LemmaDAO::_returnLemmaFromRow', array(&$lemma, &$row));
    return $bookmark;
  }
}
?>
