<?php

/**
 * @file classes/search/ArticleSearchDAO.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ArticleSearchDAO
 * @ingroup annotation
 * @see ArticleSearch
 *
 * @brief DAO class for article search index.
 */

// $Id: ArticleSearchDAO.inc.php,v 1.39 2009/05/12 14:34:54 asmecher Exp $


import('search.ArticleSearch');

class AnnotationSearchDAO extends DAO {

    function searchLemmas($query, $publishedFrom, $publishedTo) {
        $lemmaDAO = DAORegistry::getDAO('LemmaDAO');
        $lemmas = array();

        $result =& $this->retrieve("SELECT * FROM annotation_lemmas WHERE lemma_text like ?",
                array('%' . $query . '%'));

        while( ! $result->EOF) {
            $lemmas[] = $lemmaDAO->_returnLemmaFromRow($result->GetRowAssoc(false));
            $result->MoveNext();
        }
        $result->Close();
        unset($result);
        return $lemmas;
    }

    function searchNotes($query, $publishedFrom, $publishedTo) {
        $noteDAO = DAORegistry::getDAO('NoteDAO');
        $notes = array();

        $result =& $this->retrieve("SELECT * FROM annotation_notes WHERE note_text like ?",
                array('%' . $query . '%'));

        while( ! $result->EOF) {
            $notes[] = $noteDAO->_returnNoteFromRow($result->GetRowAssoc(false));
            $result->MoveNext();
        }
        $result->Close();
        unset($result);
        return $notes;
    }

    function getSearchResults($searchType, $query, $publishedFrom, $publishedTo) {
        switch($searchType) {
            case 'all':
                return array(
                        'lemmas' => $this->searchLemmas($query, $publishedFrom, $publishedTo),
                        'notes' => $this->searchNotes($query, $publishedFrom, $publishedTo)
                );
                break;
            case 'lemmas':
                return array(
                        'lemmas' => $this->searchLemmas($query, $publishedFrom, $publishedTo),
                );
                break;
            case 'notes':
                return array(
                        'notes' => $this->searchNotes($query, $publishedFrom, $publishedTo)
                );
                break;
            default:
                return array();
                break;
        }
    }
}

?>
