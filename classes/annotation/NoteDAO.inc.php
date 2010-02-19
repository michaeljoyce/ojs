<?php
/**
 * @file classes/annotation/NoteDAO.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class NoteDAO
 * @ingroup annotation
 * @see Note
 *
 * @brief Class providing OJS-specific annotation functionality.
 */

import('annotation.Note');
class NoteDAO extends DAO {

    /**
     * Insert a new note into the database, and set the note_id attribute as a side result.
     *
     * @param $note object
     * @return boolean
     */
    function insertNote(&$note) {
        $result = false;
        $result = $this->update(sprintf('INSERT INTO annotation_notes (user_id, article_id, galley_id, lemma_id, date_created, date_modified, note_text)
                                VALUES (?, ?, ?, ?, %s, %s, ?)', $this->datetimeToDB(date('U')), $this->datetimeToDB(date('U'))),
                array($note->getUserId(),
                $note->getArticleId(),
                $note->getGalleyId(),
                $note->getLemmaId(),
                $note->getNoteText()
                )
        );
        $note->setNoteId($this->getInsertId('annotation_notes', 'note_id'));
        return $result;
    }

    /**
     * update a note in the database.
     *
     * @param $note object
     * @return boolean
     */
    function updateNote(&$note) {
        $result = false;
        $result = $this->update(sprintf('UPDATE annotation_notes SET note_text = ?, date_modified = %s WHERE note_id = ? AND user_id = ?', $this->datetimeToDB(date('U'))),
                array($note->getNoteText(), $note->getNoteId(), $note->getUserId()));
        return $result;
    }

    /**
     *  fetch the notes associated with a lemma and user. In the future, users will be able to attach notes to other users lemmas, hence the $userId parameter.
     *
     * @param $lemmaId int
     * @param $userId int
     * @return array of notes
     */
    function getNotesByLemma($lemmaId, $userId) {
        $notes = array();
        $result =& $this->retrieve("SELECT * FROM annotation_notes WHERE lemma_id = ? AND user_id = ?",
                array((int) $lemmaId, (int) $userId));

        while( ! $result->EOF) {
            $notes[] = $this->_returnNoteFromRow($result->GetRowAssoc(false));
            $result->MoveNext();
        }
        $result->Close();
        unset($result);
        return $notes;
    }
    
    /**
     * Fetch a note from the database. Pass the userId to make sure that the note is owned by the correct/current user.
     * @param $noteId int
     * @param $userId int
     * @return object
     */
    function getNote($noteId, $userId) {
        $result = false;
        $result = $this->retrieve("SELECT * FROM annotation_notes WHERE note_id = ? and user_id = ?",
                array((int)$noteId, (int)$userId)
        );
        $note = null;

        if ($result->RecordCount() != 0) {
            $note =& $this->_returnNoteFromRow($result->GetRowAssoc(false));
        }

        $result->Close();
        unset($result);

        return $note;
    }

    /**
     * Delete a note from the database.
     *
     * @param $note object
     *
     * FIXME: the parameter should probably be a $noteId, rather than a note, to be consistient with the other Note::DAO functions
     */
    function deleteNote(&$note) {
        $result = $this->update('DELETE FROM annotation_notes WHERE note_id = ?',
                array((int) $note->getNoteID()));
        return $result;
    }

    /**
     * Delete all notes associated with a userId
     *
     * @param $userId int
     * @return boolean
     */
    function deleteAllNotes($userId) {
        $result = $this->update('DELETE FROM annotation_notes WHERE user_id = ?',
                array((int) $userId));
        return $result;
    }

    /**
     * Creates a new note object from the data in a DB row and returns it.
     * @param $row associateive array
     * @return object
     *
     * FIXME: why is the hookregistry::call commented out?
     */
    function &_returnNoteFromRow($row) {
        $note = new Note();
        $note->setNoteId($row['note_id']);
        $note->setUserId($row['user_id']);
        $note->setArticleId($row['article_id']);
        $note->setGalleyId($row['galley_id']);
        $note->setLemmaId($row['lemma_id']);

        $note->setDateCreated($this->datetimeFromDB($row['date_created']));
        $note->setDateModified($this->datetimeFromDB($row['date_modified']));

        $note->setNoteText($row['note_text']);

        //HookRegistry::call('NoteDAO::_returnNoteFromRow', array(&$note, &$row));
        return $note;
    }
}
?>
