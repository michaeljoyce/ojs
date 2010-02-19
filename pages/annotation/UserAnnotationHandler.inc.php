<?php

/**
 * @file UserAnnotationHandler.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class UserAnnotationHandler
 * @ingroup pages_annotation
 *
 * @brief Handle requests for user annotations.
 *
 */

// $Id: AnnotationHandler.inc.php,v 1.44 2009/09/22 21:20:36 asmecher Exp $


import('rt.ojs.RTDAO');
import('rt.ojs.JournalRT');
import('handler.Handler');

class UserAnnotationHandler extends Handler {

  var $journal;

  /**
   * Constructor
   */
  function UserAnnotationHandler() {
    parent::Handler();
    $this->journal = Request::getJournal();
  }

  /**
   * index is the default page, and synonymous with show
   * @param $args array of positional arguments
   */
  function index($args) {
    $this->show($args);
  }

  /**
   * default page - display a user's annotations for this journal or for all journals.
   * @param $args array of positional arguments
   *
   * FIXME - how do I add information to the help page?
   */
  function show($args) {
    $this->validate();
    $this->setupTemplate();
    $templateMgr =& TemplateManager::getManager();
    $journal =& Request::getJournal();
    $templateMgr->assign('helpTopicId', 'user.userAnnotations');
    $user =& Request::getUser();
    $userId = $user->getId();
    $lemmaDAO =& DAORegistry::getDAO('LemmaDAO');

    if ($this->journal == null) {
    // Curently at site level
    // fetch all of the users annotations, and assign them to a template variable here.
    // Show the author index
      $rangeInfo = Handler::getRangeInfo('userLemmas');
      $lemmas =& $lemmaDAO->getLemmasByUserRange($userId, $rangeInfo);
      $templateMgr->assign_by_ref('userLemmas', $lemmas);
    } else {
    // Currently within a journal's context.
    //fetch the users annotations for this journal, and assign them to a template variable here.
      $rangeInfo = Handler::getRangeInfo('userLemmas');
      $lemmas =& $lemmaDAO->getLemmasByUserJournalRange($userId, $this->journal->getId(), $rangeInfo);
      $templateMgr->assign_by_ref('userLemmas', $lemmas);
    }

    $templateMgr->display('annotation/show.tpl');
  }

  /**
   * FIXME make this work. There should code here.
   * @param $args array of positional arguments
   */
  function deleteAll($args) {

  }

  /**
   * Validation - make sure a user is logged in and that annotations are
   * enabled for this journal
   * @return boolean
   */
  function validate() {
    parent::validate();

    if($this->journal != null) {
      $annotationsEnabled = $this->journal->getSetting('rtAnnotationsEnabled');
      if( ! $annotationsEnabled) {
        return false;
      }
    }

    if (!Validation::isLoggedIn()) {
      return false;
    }

    return true;
  }
}
?>
