<?php

/**
 * @defgroup pages_annotation
 */

/**
 * @file pages/annotation/index.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup pages_annotation
 * @brief Handle requests for annotation functions.
 *
 */

// $Id: index.php,v 1.9 2009/04/08 19:54:33 asmecher Exp $

switch($op) {
  case 'view':
  case 'viewLemma':
  case 'saveLemma':
  case 'deleteLemma':
  case 'addNote':
  case 'editNote':
  case 'deleteNote':
    define('HANDLER_CLASS', 'AnnotationHandler');
    import('pages.annotation.AnnotationHandler');
    break;
  case 'index':
  case 'show':
  case 'deleteAll':
  default:
    define('HANDLER_CLASS', 'UserAnnotationHandler');
    import('pages.annotation.UserAnnotationHandler');
    break;
  case 'search':
  case 'results':
    define('HANDLER_CLASS', 'AnnotationSearchHandler');
    import('pages.annotation.AnnotationSearchHandler');
    break;
}

?>
