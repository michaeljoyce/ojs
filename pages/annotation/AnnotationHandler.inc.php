<?php
/**
 * @defgroup pages_annotation
 */
/**
 * @file AnnotationHandler.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AnnotationHandler
 * @ingroup pages_annotation
 *
 * @brief Handle requests for user annotations.
 *
 */

// $Id$


import('rt.ojs.RTDAO');
import('rt.ojs.JournalRT');
import('handler.Handler');

class AnnotationHandler extends Handler {
    /** issue associated with this request **/
    var $issue;

    /** article associated with this request **/
    var $article;

    /**
     * Constructor
     **/
    function AnnotationHandler() {
        parent::Handler();
    }

    /**
     * view the annotations (notes and highlights) for an article.
     * Ajax usually makes this request to insert the annotations into a document.
     * Sends an XML response.
     * @param $args array of positional arguments to this request.
     */
    function view($args) {
        //all notes and lemmas (ie. all annotations)
        $templateMgr =& TemplateManager::getManager();
        if($this->validate()) {
            $articleId = isset($args[0]) ? (int) $args[0] : 0;
            $user =& Request::getUser();
            $userId = isset($user) ? $user->getId() : null;

            $lemmaDAO =& DAORegistry::getDAO('LemmaDAO');
            $lemmas =& $lemmaDAO->getLemmasByArticle($articleId, $userId);

            if($lemmas) {
                $templateMgr->assign_by_ref("lemmas", $lemmas);
            }
        }
        $templateMgr->display('annotation/annotations.tpl', 'application/xml');
    }

    /**
     * view the XML for a single lemma
     * @param $args array of positional arguments to this request.
     */
    function viewLemma($args) {
        $templateMgr =& TemplateManager::getManager();
        if($this->validate()) {
            $lemmaId = isset($args[0]) ? (int) $args[0] : 0;
            $user =& Request::getUser();
            $userId = isset($user) ? $user->getId() : null;

            $lemmaDAO =& DAORegistry::getDAO('LemmaDAO');
            $lemma =& $lemmaDAO->getLemma($lemmaId, $userId);
            if($lemma) {
                $templateMgr->assign_by_ref("lemma", $lemma);
            }
            $noteDAO =& DAORegistry::getDAO('NoteDAO');
            $notes =& $noteDAO->getNotesByLemma($lemma->getLemmaId(), $userId);
            if($notes) {
                $templateMgr->assign_by_ref("notes", $notes);
            }
            $templateMgr->display("annotation/lemma.tpl", 'application/xml');
        }
    }

    /**
     * save a lemma into the database, and return the XML for that saved lemma.
     * Ajax makes the request, gets the response and applies it to the document.
     * @param $args array of positional arguments
     */
    function saveLemma($args) {
        $articleId = isset($args[0]) ? (int) $args[0] : 0;
        if($this->validate()) {
            $templateMgr =& TemplateManager::getManager();
            $user =& Request::getUser();
            $userId = isset($user) ? $user->getId() : null;

            $lemmaDAO =& DAORegistry::getDAO('LemmaDAO');
            $lemma = new Lemma();
            $lemma->setUserId($userId);
            $lemma->setArticleId($articleId);
            $lemma->setGalleyId(Request::getUserVar('galleyId'));
            $lemma->setStartContainer(Request::getUserVar('startContainer'));
            $lemma->setEndContainer(Request::getUserVar('endContainer'));
            $lemma->setStartOffset(Request::getUserVar('startOffset'));
            $lemma->setEndOffset(Request::getUserVar('endOffset'));
            $lemma->setLemmaType(Request::getUserVar('lemmaType'));
            $lemma->setLemmaText(Request::getUservar('textContent'));
            $lemmaDAO->insertLemma($lemma);
            $templateMgr->assign_by_ref("lemma", $lemma);
            $templateMgr->display("annotation/lemma.tpl", 'application/xml');
        }
    }

    /**
     * Delete a lemma from the database. This is made by a normal request (not ajax)
     *
     * @param $args array of positional arguments
     *
     * FIXME why does this say $noteForm when it's deleting a lemma?
     */
    function deleteLemma($args) {
        $articleId = isset($args[0]) ? (int) $args[0] : 0;
        $galleyId = isset($args[1]) ? (int) $args[1] : 0;
        $lemmaId = isset($args[2]) ? (int) $args[2] : 0;

        if($this->validate()) {
            $user =& Request::getUser();
            $lemmaDao =& DAORegistry::getDAO('LemmaDAO');
            $lemma =& $lemmaDao->getLemma($lemmaId, $user->getId());
            if((! isset($lemma)) || ($lemma->getArticleId() != $articleId)) {
                Request::redirect(null, null, 'view', array($articleId, $galleyId));
            }
            import('annotation.form.NoteForm');
            $noteForm = new NoteForm($articleId, $galleyId, $lemmaId, null, 'annotation/LemmaFormDelete.tpl');
            $noteForm->initData();
            if(isset($args[3]) && $args[3] == 'delete') {
                $noteForm->readInputData();
                if($noteForm->validate()) {
                    $noteForm->execute('deleteLemma');
                    Request::redirect(null, 'article', 'view', array($articleId, $galleyId), array('refresh' => 1));
                }
            }
            $this->setupTemplate(null, $galleyId, $lemma);
            $noteForm->display();
        }
    }

    /**
     * Add a note to a lemma. Display the form or save the result.
     * @param $args array of positional arguments to this request.
     */
    //addNote/$articleId/$galleyId.$lemmaId/save
    function addNote($args) {
        $articleId = isset($args[0]) ? (int) $args[0] : 0;
        $galleyId = isset($args[1]) ? (int) $args[1] : 0;
        $lemmaId = isset($args[2]) ? (int) $args[2] : 0;

        if($this->validate()) {
            $user =& Request::getUser();

            // Bring in note constants
            $noteDao =& DAORegistry::getDAO('NoteDAO');
            $lemmaDao =& DAORegistry::getDAO('LemmaDAO');

            $lemma =& $lemmaDao->getLemma($lemmaId, $user->getId());
            if ((! isset($lemma)) || ($lemma->getArticleId() != $articleId)) {
                Request::redirect(null, null, 'view', array($articleId, $galleyId));
            }

            import('annotation.form.NoteForm');
            $noteForm = new NoteForm($articleId, $galleyId, $lemmaId);
            $noteForm->initData();

            if (isset($args[3]) && $args[3]=='save') {
                $noteForm->readInputData();
                if ($noteForm->validate()) {
                    $noteForm->execute();
                    Request::redirect(null, 'article', 'view', array($articleId, $galleyId), array('refresh' => 1));
                }
            }

            $this->setupTemplate(null, $galleyId, $lemma);
            $noteForm->display();
        }
    }

    /**
     * Edit the content of a note. 
     * @param $args array of positional arguments to this request.
     */
    function editNote($args) {
        $articleId = isset($args[0]) ? (int) $args[0] : 0;
        $galleyId = isset($args[1]) ? (int) $args[1] : 0;
        $lemmaId = isset($args[2]) ? (int) $args[2] : 0;
        $noteId = isset($args[3]) ? (int) $args[3] : 0;

        if($this->validate()) {
            $user =& Request::getUser();

            $lemmaDao =& DAORegistry::getDAO('LemmaDAO');

            $lemma =& $lemmaDao->getLemma($lemmaId, $user->getId());
            if ((! isset($lemma)) || ($lemma->getArticleId() != $articleId)) {
                Request::redirect(null, null, 'view', array($articleId, $galleyId));
            }
            import('annotation.form.NoteForm');
            $noteForm = new NoteForm($articleId, $galleyId, $lemmaId, $noteId);
            $noteForm->initData();

            if (isset($args[4]) && $args[4]=='save') {
                $noteForm->readInputData();
                if ($noteForm->validate()) {
                    $noteForm->execute();
                    Request::redirect(null, 'article', 'view', array($articleId, $galleyId), array('refresh' => 1));
                }
            }

            $this->setupTemplate(null, $galleyId, $lemma);
            $noteForm->display();
        }
    }

    /**
     * Delete a note.
     * @param $args array of positional arguments to this request.
     */
    function deleteNote($args) {
        $articleId = isset($args[0]) ? (int) $args[0] : 0;
        $galleyId = isset($args[1]) ? (int) $args[1] : 0;
        $lemmaId = isset($args[2]) ? (int) $args[2] : 0;
        $noteId = isset($args[3]) ? (int) $args[3] : 0;

        if($this->validate()) {
            $user =& Request::getUser();

            $lemmaDao =& DAORegistry::getDAO('LemmaDAO');

            $lemma =& $lemmaDao->getLemma($lemmaId, $user->getId());
            if ((! isset($lemma)) || ($lemma->getArticleId() != $articleId)) {
                Request::redirect(null, null, 'view', array($articleId, $galleyId));
            }
            import('annotation.form.NoteForm');
            $noteForm = new NoteForm($articleId, $galleyId, $lemmaId, $noteId, 'annotation/NoteFormDelete.tpl');
            $noteForm->initData();

            if (isset($args[4]) && $args[4]=='delete') {
                $noteForm->readInputData();
                if ($noteForm->validate()) {
                    $noteForm->execute('delete');
                    Request::redirect(null, 'article', 'view', array($articleId, $galleyId), array('refresh' => 1));
                }
            }

            $this->setupTemplate(null, $galleyId, $lemma);
            $noteForm->display();
        }

    }

    /**
     * Validation - make sure the journal's annotations are enabled, and that the user is logged in.
     * @return boolean
     */
    function validate() {
        parent::validate();

        $journal =& Request::getJournal();

        $annotationsEnabled = $journal->getSetting('rtAnnotationsEnabled');
        if( ! $annotationsEnabled) {
            return false;
        }

        if (!Validation::isLoggedIn()) {
            return false;
        }

        return true;
    }
}
?>
