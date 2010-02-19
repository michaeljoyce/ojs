<?php
/**
 * @defgroup annotation
 */

/**
 * @file classes/annotation/AnnotationSearch.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AnnotationSearch
 * @ingroup annotation
 * @see AnnotationSearchDAO
 *
 * @brief Class for retrieving annotation search results.
 *
 */

// $Id$

import("search.ArticleSearch");

class AnnotationSearch {

    /**
     * get an array of search results from the DAO class
     * @param $searchType string one of note, lemma, or all
     * @param $query string the search terms to find
     * @param $publsihedFrom string null, or a date from which to start the search
     * @param $publishedTo string null, or a date to limit the search results
     * @return array
     */
    function &_getArray($searchType, $query, $publishedFrom, $publishedTo) {
        $annotationSearchDAO = DAORegistry::getDAO("AnnotationSearchDAO");
        $results = $annotationSearchDAO->getSearchResults($searchType, $query, $publishedFrom, $publishedTo);
        return $results;
    }

    /**
     * Fetch search results from the database, and apply paging
     * 
     * @param $searchType string one of note, lemma, or all
     * @param $query string the search terms to find
     * @param $publsihedFrom string null, or a date from which to start the search
     * @param $publishedTo string null, or a date to limit the search results
     * @param $rangeInfo object pagination information
     * @return array
     *
     * FIXME: should this really be paginating both the lemma and note results? Is should probably paginate the union of the two.
     */
    function &retrieveResults($searchType, $query, $publishedFrom = null, $publishedTo = null, $rangeInfo = null) {
        $results =& AnnotationSearch::_getArray($searchType, $query, $publishedFrom, $publishedTo);

        $totalResults = count($results);
        $page = 1;
        $itemsPerPage = max($totalResults, 1);

        // Use only the results for the specified page, if specified.
        if ($rangeInfo && $rangeInfo->isValid()) {
            $results['lemmas'] = array_slice(
                    $results['lemmas'],
                    $rangeInfo->getCount() * ($rangeInfo->getPage()-1),
                    $rangeInfo->getCount()
            );
            $results['notes'] = array_slice(
                    $results['notes'],
                    $rangeInfo->getCount() * ($rangeInfo->getPage()-1),
                    $rangeInfo->getCount()
            );
            $page = $rangeInfo->getPage();
            $itemsPerPage = $rangeInfo->getCount();
        }

        return $results;
    }
}

?>
