<?php

class Bookmark extends DataObject {
//put your code here
    function setBookmarkId($bookmarkId) {
        $this->setData('bookmarkId', $bookmarkId);
    }

    function getBookmarkId() {
        return $this->getData('bookmarkId');
    }

    function setUserId($userId) {
        $this->setData('userId', $userId);
    }

    function getUserId() {
        return $this->getData('userId');
    }

    function setArticleId($articleId) {
        $this->setData('articleId', $articleId);
    }

    function getArticleId() {
        return $this->getData('articleId');
    }

    function setGalleyId($galleyId) {
        $this->setData('galleyId', $galleyId);
    }

    function getGalleyId() {
        return $this->getData('galleyId');
    }

    function setContainer($container) {
        $this->setData('container', $container);
    }

    function getContainer() {
        return $this->getData('container');
    }

    function setBookmarkType($bookmarkType) {
        $this->setData('bookmarkType', $bookmarkType);
    }

    function getBookmarkType() {
        return $this->getData('bookmarkType');
    }

    function setDescription($description) {
        $this->setData('description', $description);
    }

    function getDescription() {
        return $this->getData('description');
    }


}
?>
