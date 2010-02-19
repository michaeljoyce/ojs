<?php
/**
 * @file classes/annotation/Lemma.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Lemma
 * @ingroup annotation
 * @see LemmaDAO
 *
 * @brief Class for annotation lemmas (highlights) associated with article.
 */

// $Id$
class Lemma extends DataObject {

    /**
     * Constructor
     */
    function Lemma() {
        parent::DataObject();
    }

    /**
     * set the lemma id
     * @param $lemmaId int
     */
    function setLemmaId($lemmaId) {
        $this->setData('lemmaId', $lemmaId);
    }

    /**
     * return the id of the lemma
     * @return int
     */
    function getLemmaId() {
        return $this->getData('lemmaId');
    }

    /**
     * Set the ID of the user who created this lemma
     * @param $userId int
     */
    function setUserId($userId) {
        $this->setData('userId', $userId);
    }

    /**
     * Get the ID of the user who created this lemma
     * @return int
     */
    function getUserId() {
        return $this->getData('userId');
    }

    /**
     * set the id of the article associated with this lemma
     * @param $articleId int
     */
    function setArticleId($articleId) {
        $this->setData('articleId', $articleId);
    }

    /**
     * get the id of the article associated with this lemma
     * @return int
     */
    function getArticleId() {
        return $this->getData('articleId');
    }

    /**
     * get the id of the galley associated with this lemma
     *
     * @param $galleyId int
     */
    function setGalleyId($galleyId) {
        $this->setData('galleyId', $galleyId);
    }

    /**
     * get the id of the galley associated with this lemma
     * @return int
     */
    function getGalleyId() {
        return $this->getData('galleyId');
    }

    /**
     * Set the ID of the HTML element in the galley that contains the start of a lemma
     * @param $startContainer string
     */
    function setStartContainer($startContainer) {
        $this->setData('startContainer', $startContainer);
    }

    /**
     * Get the ID of the HTML element in the galley that contains the start of a lemma
     * @return string
     */
    function getStartContainer() {
        return $this->getData('startContainer');
    }

    /**
     * Set the ID of the HTML element in the galley that contains the end of the lemma
     * @param $endContainer string
     */
    function setEndContainer($endContainer) {
        $this->setData('endContainer', $endContainer);
    }

    /**
     * get the id of the html element in the galley that contains the end of the lemma
     * @return string
     */
    function getEndContainer() {
        return $this->getData('endContainer');
    }

    /**
     * set the offset (number of characters from the start of the element) of the start of the lemma
     * @param $startOffset string
     */
    function setStartOffset($startOffset) {
        $this->setData('startOffset', $startOffset);
    }

    /**
     * Get the offset of the start of the lemma
     * @return string
     */
    function getStartOffset() {
        return $this->getData('startOffset');
    }

    /**
     * Set the offset (number of characters from the start of the element) of the end of the lemma
     * @param $endOffset int
     */
    function setEndOffset($endOffset) {
        $this->setData('endOffset', $endOffset);
    }

    /**
     * Get the offset of the end of the lemma
     * @return int
     */
    function getEndOffset() {
        return $this->getData('endOffset');
    }

    /**
     * Get the date that the lemma was created
     * @return string
     */
    function getDateCreated() {
        return $this->getData('dateCreated');
    }

    /**
     * Set the date that the lemma was created
     * @param $dateCreated string
     */
    function setDateCreated($dateCreated) {
        $this->setData('dateCreated', $dateCreated);
    }

    /**
     * Get the date that a lemma was modified
     * @return string
     * FIXME: how do you modify a lemma? I don't think it's possible.
     */
    function getDateModified() {
        return $this->getData('dateModified');
    }

    /**
     * Set the modified date of a lemma.
     * @param $dateModified string
     */
    function setDateModified($dateModified) {
        $this->setData('dateModified', $dateModified);
    }

    /**
     * Set the lemma type. This should match a class in the CSS file
     * @param $lemmaType string
     */
    function setLemmaType($lemmaType) {
        $this->setData('lemmaType', $lemmaType);
    }

    /**
     * Get the lemma type. This should match a class in the css file.
     * @return string
     */
    function getLemmaType() {
        return $this->getData('lemmaType');
    }

    /**
     * Set the text that this lemma highlights
     * @param $lemmaText string
     */
    function setLemmaText($lemmaText) {
        $this->setData('lemmaText', $lemmaText);
    }

    /**
     * Get the text that this lemma highlights
     * @return string
     */
    function getLemmaText() {
        return $this->getData('lemmaText');
    }

    //--------------------------------

    /**
     * Set the notes associated with this lemma
     * @param $notes array of note objects
     */
    function setLemmaNotes($notes) {
        $this->setData('lemmaNotes', $notes);
    }

    /**
     * get the notes associated with this lemma
     * @return array of note objects
     */
    function getLemmaNotes() {
        return $this->getData('lemmaNotes');
    }

    /**
     * Set the journal associated with this lemma
     * @param $journal object
     */
    function setLemmaJournal($journal) {
        $this->setData('lemmaJournal', $journal);
    }

    /**
     * Get the journal associated with this lemma
     * @return $journal object
     */
    function getLemmaJournal() {
        return $this->getData('lemmaJournal');
    }

    /**
     * Set the article associated with this lemma
     * @param $article
     */
    function setLemmaArticle($article) {
        $this->setData('lemmaArticle', $article);
    }

    /**
     * get the article associated with this lemma
     * @return $article
     */
    function getLemmaArticle() {
        return $this->getData('lemmaArticle');
    }
}
?>
