<?php
/**
 * @file classes/annotation/LemmaDAO.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class LemmaDAO
 * @ingroup annotation
 * @see Lemma
 *
 * @brief Class for public Comment associated with article.
 */

// $Id$

import('annotation.Lemma');
import('annotation.Note');
import('annotation.NoteDAO');
import('article.Article');
import('article.ArticleDAO');
import('journal.Journal');
import('journal.JournalDAO');

class LemmaDAO extends DAO {

    /**
     * Insert a new lemma into the database, and set the lemmaId as a side effect.
     * @param $lemma object
     * @return boolean
     */
    function insertLemma(&$lemma) {
        $result = false;
        $result = $this->update(
                sprintf('INSERT INTO annotation_lemmas (user_id, article_id, galley_id, start_container, end_container, start_offset, end_offset, date_created, date_modified, lemma_type, lemma_text) VALUES
        (?, ?, ?, ?, ?, ?, ?, %s, %s, ?, ?)', $this->datetimeToDB(date('U')), $this->datetimeToDB(date('U'))),
                array($lemma->getUserId(),
                $lemma->getArticleId(),
                $lemma->getGalleyId(),
                $lemma->getStartContainer(),
                $lemma->getEndContainer(),
                $lemma->getStartOffset(),
                $lemma->getEndOffset(),
                $lemma->getLemmaType(),
                $lemma->getLemmaText()
                )
        );
        $lemma->setDateCreated(date('U'));
        $lemma->setDateModified(date('U'));
        $lemma->setLemmaId($this->getInsertId('annotation_lemmas', 'lemma_id'));
        return $result;
    }

    /**
     * Fetch a Lemma object from the database.
     * @param $lemmaId int
     * @param $userId int
     * @return object
     */
    function getLemma($lemmaId, $userId) {
        $lemma = null;
        $result =& $this->retrieve("SELECT * FROM annotation_lemmas WHERE lemma_id = ? AND user_id = ?", array((int) $lemmaId, (int) $userId));
        while( ! $result->EOF) {
            $lemma = $this->_returnLemmaFromRow($result->GetRowAssoc(false));
            $result->MoveNext();
        }
        $result->Close();
        unset($result);
        return $lemma;
    }

    /**
     * Fetch an array of lemma objects for an article, for one user. Returns all the lemmas, even ones for the abstract. Take care which ones you apply to a galley.
     *
     * @param $articleId int
     * @param $userId int
     * @return array
     *
     * FIXME: should this accept a galleyId instead?
     */
    function getLemmasByArticle($articleId, $userId) {
        $lemmas = array();
        $result =& $this->retrieve("SELECT * FROM annotation_lemmas WHERE article_id = ? AND user_id = ?", array((int) $articleId, (int) $userId));
        while( ! $result->EOF) {
            $lemma = $this->_returnLemmaFromRow($result->GetRowAssoc(false));
            $lemmas[] = $lemma;
            $result->MoveNext();
        }
        $result->Close();
        unset($result);
        return $lemmas;
    }

    /**
     * Get all the lemmas for a user, possibly limiting to the $limit most recent
     *
     * @param $userId int
     * @param $limit int
     * @return array
     */
    function getLemmasByUser($userId, $limit = 0) {
        $lemmas = array();
        $limit_sql = "";
        if($limit != 0) {
            $limit_sql = " LIMIT $limit";
        } else {
            $limit_sql = "";
        }
        $result =& $this->retrieve("SELECT * FROM annotation_lemmas WHERE user_id = ? ORDER BY lemma_id DESC" . $limit_sql,
                array((int) $userId)
        );
        while( ! $result->EOF) {
            $lemma = $this->_returnLemmaFromRow($result->getRowAssoc(false));
            $lemmas[] = $lemma;
            $result->MoveNext();
        }
        $result->Close();
        unset($result);
        return $lemmas;
    }

    /**
     * Fetch an iterator to page through a users lemmas.
     *
     * @param $userId int
     * @param $rangeInfo object
     * @return object
     */
    function getLemmasByUserRange($userId, $rangeInfo = null) {
        $result =& $this->retrieveRange("SELECT * FROM annotation_lemmas WHERE user_id = ? ORDER BY lemma_id DESC",
                array((int) $userId), $rangeInfo);
        $returner = new DAOResultFactory($result, $this, '_returnLemmaFromRow');
        return $returner;
    }

    /**
     * Fetch a users lemmas for a journal, optionally limited to $limit most recent lemmas.
     * @param $userId int
     * @param $journalId int
     * @param $limit int
     * @return array
     */
    function getLemmasByUserJournal($userId, $journalId, $limit) {
        $lemmas = array();
        $limit_sql = "";
        if($limit != 0) {
            $limit_sql = " LIMIT $limit";
        } else {
            $limit_sql = "";
        }
        $result =& $this->retrieve("SELECT * FROM articles, annotation_lemmas WHERE annotation_lemmas.user_id = ? AND articles.article_id = annotation_lemmas.article_id AND articles.journal_id = ? ORDER BY lemma_id DESC " . $limit_sql,
                array((int) $userId, (int) $journalId)
        );
        while( ! $result->EOF) {
            $lemma = $this->_returnLemmaFromRow($result->getRowAssoc(false));
            $lemmas[] = $lemma;
            $result->MoveNext();
        }
        $result->Close();
        unset($result);
        return $lemmas;
    }

    /**
     * Fetch an iterator of lemma objects for a user in a particular journal, which is suitable for paging
     * @param $userId int
     * @param $journalId int
     * @param $rangeInfo object
     * @return object
     */
    function getLemmasByUserJournalRange($userId, $journalId, $rangeInfo = null) {
        $result =& $this->retrieveRange("SELECT * FROM articles, annotation_lemmas WHERE annotation_lemmas.user_id = ? AND articles.article_id = annotation_lemmas.article_id AND articles.journal_id = ? ORDER BY lemma_id DESC",
                array((int) $userId, (int) $journalId), $rangeInfo);
        $returner = new DAOResultFactory($result, $this, '_returnLemmaFromRow');
        return $returner;
    }

    /**
     * Delete a lemma, and the notes associated with that lemma.
     * Checks to make sure that the lemma being deleted belongs to the current user.
     *
     * @param $lemmaId int
     * @return boolean
     *
     * FIXME: is it necessary to check that the current user is the one that owns the lemma?
     */
    function deleteLemma($lemmaId) {
        $user = Request::getUser();
        $noteDAO =& DAORegistry::getDAO('NoteDAO');
        $notes = $noteDAO->getNotesByLemma($lemmaId, $user->getId());
        for($i = 0; $i < count($notes); $i++) {
            $noteDAO->deleteNote($notes[$i]);
        }
        $result =& $this->update('DELETE FROM annotation_lemmas WHERE lemma_id = ? AND user_id = ?',
                array((int) $lemmaId, (int) $user->getId()));
        return $result;
    }

    /**
     * Delete all of a user's lemmas.
     *
     * @param $userId int
     * @return boolean
     */
    function deleteAllLemmas($userId) {
        $result =& $this->update('DELETE FROM annotation_lemmas WHERE user_id = ?',
                array((int) $userId)
        );
        return $result;
    }

    /**
     * Create and return a new lemma object from a row in the database.
     * 
     * @param $row
     * @return lemma
     *
     * FIXME: why is the HookRegistry::call() commented out?
     */
    function &_returnLemmaFromRow($row) {
        $lemma = new Lemma();
        $lemma->setArticleId($row['article_id']);
        $lemma->setLemmaId($row['lemma_id']);
        $lemma->setUserId($row['user_id']);
        $lemma->setGalleyId($row['galley_id']);
        $lemma->setStartContainer($row['start_container']);
        $lemma->setEndContainer($row['end_container']);
        $lemma->setStartOffset($row['start_offset']);
        $lemma->setEndOffset($row['end_offset']);

        $lemma->setDateCreated($this->datetimeFromDB($row['date_created']));
        $lemma->setDateModified($this->dateFromDB($row['date_modified']));

        $lemma->setLemmaType($row['lemma_type']);
        $lemma->setLemmaText($row['lemma_text']);

        $user =& Request::getUser();
        $userId = $user->getId();

        $noteDAO =& DAORegistry::getDAO('NoteDAO');
        $notes =& $noteDAO->getNotesByLemma($lemma->getLemmaId(), $userId);
        $lemma->setLemmaNotes($notes);

        $publishedArticleDAO =& DAORegistry::getDAO('PublishedArticleDAO');
        $article =& $publishedArticleDAO->getPublishedArticleByArticleId($lemma->getArticleId(), null, true);
        $lemma->setLemmaArticle($article);

        $journalDAO =& DAORegistry::getDAO('JournalDAO');
        $journal =& $journalDAO->getJournal($article->getJournalId());
        $lemma->setLemmaJournal($journal);

        //HookRegistry::call('LemmaDAO::_returnLemmaFromRow', array(&$lemma, &$row));
        return $lemma;
    }
}
?>
