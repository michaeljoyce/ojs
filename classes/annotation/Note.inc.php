<?php
/**
 * @file classes/annotation/Note.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Note
 * @ingroup annotation
 * @see NoteDAO
 *
 * @brief Class for notes associated with lemmas in an article.
 */

// $Id$

class Note extends DataObject {

    /**
     * Constructor
     */
    function Note() {
        parent::DataObject();
    }

    /**
     * Set the note Id of this object
     * @param $noteId int
     */
    function setNoteId($noteId) {
        $this->setData('noteId', $noteId);
    }

    /**
     * Return the note Id of this object
     * @return int
     */
    function getNoteId() {
        return $this->getData('noteId');
    }

    /**
     * Set the id of the user who created this note
     * @param $userId int
     */
    function setUserId($userId) {
        $this->setData('userId', $userId);
    }

    /**
     * Get the id of the user who created this note.
     * @return int
     */
    function getUserId() {
        return $this->getData('userId');
    }

    /**
     * Set the id of the article associated with this note
     * @param $articleId int
     */
    function setArticleId($articleId) {
        $this->setData('articleId', $articleId);
    }

    /**
     * Get the id of the article associated with this note
     * @return int
     */
    function getArticleId() {
        return $this->getData('articleId');
    }

    /**
     * Set the id of the galley associated with this note.
     * @param $galleyId int
     */
    function setGalleyId($galleyId) {
        $this->setData('galleyId', $galleyId);
    }

    /**
     * Get the id of the galley associated with this note.
     * @return int
     */
    function getGalleyId() {
        return $this->getData('galleyId');
    }

    /**
     * Set the id of the lemma associated with this note.
     * @param $galleyId int
     */
    function setLemmaId($lemmaId) {
        $this->setData('lemmaId', $lemmaId);
    }

    /**
     * Get the id of the lemma associated with this note.
     * @return int
     */
    function getLemmaId() {
        return $this->getData('lemmaId');
    }

    /**
     * get the date this note was created
     * @return string
     */
    function getDateCreated() {
        return $this->getData('dateCreated');
    }

    /**
     * set the date that this note was created
     * @param $dateCreated string
     */
    function setDateCreated($dateCreated) {
        $this->setData('dateCreated', $dateCreated);
    }

    /**
     * Get date that this note was last modified
     * @return string
     */
    function getDateModified() {
        return $this->getData('dateModified');
    }

    /**
     * Set date that this note was last modified
     * @param $dateModified  string
     */
    function setDateModified($dateModified) {
        $this->setData('dateModified', $dateModified);
    }

    /**
     * Set the text of the note
     * @param $noteText string
     */
    function setNoteText($noteText) {
        $this->setData('noteText', $noteText);
    }

    /**
     * Get the note's text (which can be HTML)
     * @return string
     */
    function getNoteText() {
        return $this->getData('noteText');
    }
}
?>
