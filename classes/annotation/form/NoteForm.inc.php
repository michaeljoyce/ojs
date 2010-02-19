<?php

/**
 * @defgroup rt_ojs_form
 */

/**
 * @file classes/note/form/NoteForm.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class NoteForm
 * @ingroup rt_ojs_form
 * @see Note, NoteDAO
 *
 * @brief Form to change metadata information for an RT note.
 */

// $Id: NoteForm.inc.php,v 1.26 2009/05/12 16:57:20 asmecher Exp $


import('form.Form');

class NoteForm extends Form {

/** @var int the ID of the note */
  var $noteId;

  /** @var int the ID of the article */
  var $articleId;

  /** @var Note current note */
  var $note;

  /** @var int lemma ID  */
  var $lemmaId;

  /** @var int Galley view by which the user entered the note page */
  var $galleyId;

  /**
   * Constructor.
   */
  function NoteForm($articleId, $galleyId, $lemmaId, $noteId = null, $template = 'annotation/NoteForm.tpl') {
    parent::Form($template);

    $this->noteId = $noteId;
    $this->articleId = $articleId;
    $this->galleyId = $galleyId;
    $this->lemmaId = $lemmaId;

    $user =& Request::getUser();
    $noteDao =& DAORegistry::getDAO('NoteDAO');
    $this->note =& $noteDao->getNote($noteId, $user->getId());

    if (isset($this->note)) {
      $this->noteId = $noteId;
    }

    $this->addCheck(new FormValidatorPost($this));
  }

  /**
   * Initialize form data from current note.
   */
  function initData() {
    $user =& Request::getUser();
    $userId = $user->getId();
    if (isset($this->note)) {
      $note =& $this->note;
      $this->_data = array(
          'noteText' => $note->getNoteText(),
      );
    } else {
      $noteDao =& DAORegistry::getDAO('NoteDAO');
      $note =& $noteDao->getNote($this->noteId, $userId);
      $this->_data = array(
          'noteText' => ''
      );
    }
  }

  /**
   * Display the form.
   */
  function display() {
    $journal = Request::getJournal();
    $templateMgr =& TemplateManager::getManager();

    if (isset($this->note)) {
      $templateMgr->assign_by_ref('note', $this->note);
      $templateMgr->assign('noteId', $this->noteId);
    }

    $user = Request::getUser();
    $lemmaDAO = DAORegistry::getDAO('LemmaDAO');
    $lemma = $lemmaDAO->getLemma($this->lemmaId, $user->getId());

    $templateMgr->assign('lemmaId', $this->lemmaId);
    $templateMgr->assign_by_ref('lemma', $lemma);
    $templateMgr->assign('articleId', $this->articleId);
    $templateMgr->assign('galleyId', $this->galleyId);

    $templateMgr->assign('enableNotes', $journal->getSetting('rtAnnotationsEnabled'));

    parent::display();
  }


  /**
   * Assign form data to user-submitted data.
   */
  function readInputData() {
    $userVars = array(
        'noteText'
    );
    $this->readUserVars($userVars);
  }

  /**
   * Save changes to note.
   * @return int the note ID
   */
  function execute($action='save') {
    $journal =& Request::getJournal();
    $enableNotes = $journal->getSetting('rtAnnotationsEnabled');

    $noteDao =& DAORegistry::getDAO('NoteDAO');

    $note = $this->note;
    if (!isset($note)) {
      $note = new Note();
    }
    switch($action) {
      case 'save':
        $user =& Request::getUser();
        $note->setNoteText($this->getData('noteText'));
        $note->setUserId($user->getId());
        $note->setlemmaId($this->lemmaId);
        $note->setGalleyId($this->galleyId);
        if (isset($this->note)) {
          $noteDao->updateNote($note);
        } else {
          $note->setArticleId($this->articleId);
          $noteDao->insertNote($note);
          $this->noteId = $note->getNoteId();
        }
        return $this->noteId;
      case 'delete':
        if(isset($this->note)) {
          $noteDao->deleteNote($note);
        }
        break;
      case 'deleteLemma':
        if(isset($this->lemmaId)) {
          $lemmaDAO =& DAORegistry::getDAO('LemmaDAO');
          $lemmaDAO->deleteLemma($this->lemmaId);
        }
    }
  }
}

?>
