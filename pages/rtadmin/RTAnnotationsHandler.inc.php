<?php

/**
 * @file RTAnnotationsHandler.inc.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RTAdminHandler
 * @ingroup pages_rtadmin
 *
 * @brief Handle Annotation Tools requests -- setup section.
 */

// $Id: RTSharingHandler.inc.php,v 1.2 2009/08/07 21:19:16 asmecher Exp $


import('rt.ojs.JournalRTAdmin');
import('rtadmin.RTAdminHandler');
import('rt.ojs.AnnotationsRT');

class RTAnnotationsHandler {
    function settings() {
        RTAdminHandler::validate();
        $journal = Request::getJournal();
        if ($journal) {
            RTAdminHandler::setupTemplate(true);
            $templateMgr =& TemplateManager::getManager();

            $rtDao =& DAORegistry::getDAO('RTDAO');
            $rt = $rtDao->getJournalRTByJournal($journal);

            $templateMgr->assign('annotationsEnabled', $rt->getAnnotationsEnabled());

            $templateMgr->assign('helpTopicId', 'journal.managementPages.readingTools.annotationsSettings');
            $templateMgr->display('rtadmin/annotations.tpl');
        } else {
            Request::redirect(null, Request::getRequestedPage());
        }
    }

    function saveSettings() {
        RTAdminHandler::validate();

        $journal = Request::getJournal();

        if ($journal) {
            $rtDao =& DAORegistry::getDAO('RTDAO');
            $rt = $rtDao->getJournalRTByJournal($journal);

            $rt->setAnnotationsEnabled(Request::getUserVar('annotationsEnabled'));

            $rtDao->updateJournalRT($rt);
        }
        Request::redirect(null, Request::getRequestedPage());
    }
}

?>
