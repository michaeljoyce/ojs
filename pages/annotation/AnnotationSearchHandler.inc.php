<?php

/**
 * @file SearchHandler.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SearchHandler
 * @ingroup pages_search
 *
 * @brief Handle site index requests.
 */

// $Id: SearchHandler.inc.php,v 1.58 2009/08/14 22:47:05 asmecher Exp $


import('search.ArticleSearch');
import('annotation.AnnotationSearch');
import('handler.Handler');

class AnnotationSearchHandler extends Handler {
    /**
     * Constructor
     **/
    function SearchHandler() {
        parent::Handler();
        $this->addCheck(new HandlerValidatorCustom($this, false, null, null, create_function('$journal', 'return !$journal || $journal->getSetting(\'publishingMode\') != PUBLISHING_MODE_NONE;'), array(Request::getJournal())));
    }

    /**
     * Show the advanced form
     */
    function search() {
        $this->validate();
        $this->setupTemplate(false);
        $templateMgr =& TemplateManager::getManager();
        $publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');

        if (Request::getJournal() == null) {
            $journalDao =& DAORegistry::getDAO('JournalDAO');
            $journals =& $journalDao->getEnabledJournalTitles();  //Enabled added
            $templateMgr->assign('siteSearch', true);
            $templateMgr->assign('journalOptions', array('' => Locale::Translate('search.allJournals')) + $journals);
            $journalPath = Request::getRequestedJournalPath();
            $yearRange = $publishedArticleDao->getArticleYearRange(null);
        } else {
            $journal =& Request::getJournal();
            $yearRange = $publishedArticleDao->getArticleYearRange($journal->getJournalId());
        }

        $this->assignAdvancedSearchParameters($templateMgr, $yearRange);

        $templateMgr->display('annotation/search.tpl');
    }

    /**
     * Show basic search results.
     */
    function results() {
        $this->validate();
        $this->setupTemplate(true);

        $rangeInfo = Handler::getRangeInfo('search');
        $searchType = Request::getUserVar('annotationType');

        // Load the keywords array with submitted values
        $query = Request::getUserVar('query');

        //msj - change this to AnnotationSearch::retrieveResults once it is written.
        $results =& AnnotationSearch::retrieveResults($searchType, $query, null, null, $rangeInfo);
        logMsg('results:: ' . print_r($results, true));

        $templateMgr =& TemplateManager::getManager();
        $templateMgr->setCacheability(CACHEABILITY_NO_STORE);
        $templateMgr->assign_by_ref('lemmas', $results['lemmas']);
        $templateMgr->assign_by_ref('notes', $results['notes']);
        $templateMgr->assign('basicQuery', Request::getUserVar('query'));
        $templateMgr->display('annotation/results.tpl');
    }



    /**
     * Setup common template variables.
     * @param $subclass boolean set to true if caller is below this handler in the hierarchy
     */
    function setupTemplate($subclass = false) {
        parent::setupTemplate();
        $templateMgr =& TemplateManager::getManager();
        $templateMgr->assign('helpTopicId', 'user.annotationSearch');
        $templateMgr->assign('pageHierarchy',
                $subclass ? array(array(Request::url(null, 'search'), 'navigation.annotationSearch'))
                : array()
        );

        $journal =& Request::getJournal();
        if (!$journal || !$journal->getSetting('restrictSiteAccess')) {
            $templateMgr->setCacheability(CACHEABILITY_PUBLIC);
        }
    }

    function assignAdvancedSearchParameters(&$templateMgr, $yearRange) {
        $templateMgr->assign('query', Request::getUserVar('query'));
        $templateMgr->assign('searchJournal', Request::getUserVar('searchJournal'));
        $templateMgr->assign('author', Request::getUserVar('author'));
        $templateMgr->assign('title', Request::getUserVar('title'));
        $templateMgr->assign('fullText', Request::getUserVar('fullText'));
        $templateMgr->assign('supplementaryFiles', Request::getUserVar('supplementaryFiles'));
        $templateMgr->assign('discipline', Request::getUserVar('discipline'));
        $templateMgr->assign('subject', Request::getUserVar('subject'));
        $templateMgr->assign('type', Request::getUserVar('type'));
        $templateMgr->assign('coverage', Request::getUserVar('coverage'));
        $fromMonth = Request::getUserVar('dateFromMonth');
        $fromDay = Request::getUserVar('dateFromDay');
        $fromYear = Request::getUserVar('dateFromYear');
        $templateMgr->assign('dateFromMonth', $fromMonth);
        $templateMgr->assign('dateFromDay', $fromDay);
        $templateMgr->assign('dateFromYear', $fromYear);
        if (!empty($fromYear)) $templateMgr->assign('dateFrom', date('Y-m-d H:i:s',mktime(0,0,0,$fromMonth==null?12:$fromMonth,$fromDay==null?31:$fromDay,$fromYear)));

        $toMonth = Request::getUserVar('dateToMonth');
        $toDay = Request::getUserVar('dateToDay');
        $toYear = Request::getUserVar('dateToYear');
        $templateMgr->assign('dateToMonth', $toMonth);
        $templateMgr->assign('dateToDay', $toDay);
        $templateMgr->assign('dateToYear', $toYear);

        $startYear = '-' . (date('Y') - substr($yearRange[1], 0, 4));
        if (substr($yearRange[0], 0, 4) >= date('Y')) {
            $endYear = '+' . (substr($yearRange[0], 0, 4) - date('Y'));
        } else {
            $endYear = (substr($yearRange[0], 0, 4) - date('Y'));
        }
        $templateMgr->assign('endYear', $endYear);
        $templateMgr->assign('startYear', $startYear);
        if (!empty($toYear)) $templateMgr->assign('dateTo', date('Y-m-d H:i:s',mktime(0,0,0,$toMonth==null?12:$toMonth,$toDay==null?31:$toDay,$toYear)));
    }

    function validate() {
        parent::validate();
        $user =& Request::getUser();
        $templateMgr =& TemplateManager::getManager();
        if($user == null) {
            $templateMgr->assign('isUserLoggedIn', false);
        } else {
            $templateMgr->assign('isUserLoggedIn', true);
        }
    }
}

?>
