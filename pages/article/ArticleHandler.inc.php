<?php

/**
 * @file ArticleHandler.inc.php
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ArticleHandler
 * @ingroup pages_article
 *
 * @brief Handle requests for article functions.
 *
 */

// $Id$


import('rt.ojs.RTDAO');
import('rt.ojs.JournalRT');
import('handler.Handler');
import('rt.ojs.SharingRT');
import('rt.ojs.AnnotationsRT');

class ArticleHandler extends Handler {
	/** journal associated with the request **/
	var $journal;

	/** issue associated with the request **/
	var $issue;

	/** article associated with the request **/
	var $article;

	/**
	 * Constructor
	 * @param $request Request
	 */
	function ArticleHandler(&$request) {
		parent::Handler($request);
		$router =& $request->getRouter();

		$this->addCheck(new HandlerValidatorJournal($this));
		$this->addCheck(new HandlerValidatorCustom($this, false, null, null, create_function('$journal', 'return $journal->getSetting(\'publishingMode\') != PUBLISHING_MODE_NONE;'), array($router->getContext($request))));
	}

	/**
	 * View Article.
	 * @param $args array
	 * @param $request Request
	 */
	function view($args, &$request) {
		$router =& $request->getRouter();

		$articleId = isset($args[0]) ? $args[0] : 0;
		$galleyId = isset($args[1]) ? $args[1] : 0;

		$this->setupTemplate();
		$this->validate($request, $articleId, $galleyId);

		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;

		$rtDao =& DAORegistry::getDAO('RTDAO');
		$journalRt = $rtDao->getJournalRTByJournal($journal);

		$galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
		if ($journal->getSetting('enablePublicGalleyId')) {
			$galley =& $galleyDao->getGalleyByBestGalleyId($galleyId, $article->getId());
		} else {
			$galley =& $galleyDao->getGalley($galleyId, $article->getId());
		}

		if (!$journalRt->getEnabled()) {
			if (!$galley || $galley->isHtmlGalley()) return $this->viewArticle($args, $request);
			else if ($galley->isPdfGalley()) return $this->viewPDFInterstitial($args, $request, $galley);
			else if ($galley->isInlineable()) {
				import('file.ArticleFileManager');
				$articleFileManager = new ArticleFileManager($article->getId());
				return $articleFileManager->viewFile($galley->getFileId());
			} else return $this->viewDownloadInterstitial($args, $request, $galley);
		}

		if (!$article) {
			$request->redirect(null, $router->getRequestedPage($request));
			return;
		}

		$templateMgr =& TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign_by_ref('article', $article);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign_by_ref('galley', $galley);

		$templateMgr->display('article/view.tpl');
	}

	/**
	 * Article interstitial page before PDF is shown
	 * @param $args array
	 * @param $request Request
	 * @param $galley ArticleGalley
	 */
	function viewPDFInterstitial($args, &$request, $galley = null) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$galleyId = isset($args[1]) ? $args[1] : 0;
		$this->validate($request, $articleId, $galleyId);
		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;
		$this->setupTemplate();

		if (!$galley) {
			$galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
			if ($journal->getSetting('enablePublicGalleyId')) {
				$galley =& $galleyDao->getGalleyByBestGalleyId($galleyId, $article->getId());
			} else {
				$galley =& $galleyDao->getGalley($galleyId, $article->getId());
			}
		}

		if (!$galley) $request->redirect(null, null, 'view', $articleId);

		$templateMgr =& TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign_by_ref('galley', $galley);
		$templateMgr->assign_by_ref('article', $article);

		$templateMgr->display('article/pdfInterstitial.tpl');
	}

	/**
	 * Article interstitial page before a non-PDF, non-HTML galley is
	 * downloaded
	 * @param $args array
	 * @param $request Request
	 * @param $galley ArticleGalley
	 */
	function viewDownloadInterstitial($args, &$request, $galley = null) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$galleyId = isset($args[1]) ? $args[1] : 0;
		$this->validate($request, $articleId, $galleyId);
		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;
		$this->setupTemplate();

		if (!$galley) {
			$galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
			if ($journal->getSetting('enablePublicGalleyId')) {
				$galley =& $galleyDao->getGalleyByBestGalleyId($galleyId, $article->getId());
			} else {
				$galley =& $galleyDao->getGalley($galleyId, $article->getId());
			}
		}

		if (!$galley) $request->redirect(null, null, 'view', $articleId);

		$templateMgr =& TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign_by_ref('galley', $galley);
		$templateMgr->assign_by_ref('article', $article);

		$templateMgr->display('article/interstitial.tpl');
	}

	/**
	 * Article view
	 * @param $args array
	 * @param $request Request
	 */
	function viewArticle($args, &$request) {
		$router =& $request->getRouter();
		$articleId = isset($args[0]) ? $args[0] : 0;
		$galleyId = isset($args[1]) ? $args[1] : 0;

		$this->validate($request, $articleId, $galleyId);
		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;
		$this->setupTemplate();

		$rtDao =& DAORegistry::getDAO('RTDAO');
		$journalRt = $rtDao->getJournalRTByJournal($journal);

		$sectionDao =& DAORegistry::getDAO('SectionDAO');
		$section =& $sectionDao->getSection($article->getSectionId());

		if ($journalRt->getVersion()!=null && $journalRt->getDefineTerms()) {
			// Determine the "Define Terms" context ID.
			$version = $rtDao->getVersion($journalRt->getVersion(), $journalRt->getJournalId());
			if ($version) foreach ($version->getContexts() as $context) {
				if ($context->getDefineTerms()) {
					$defineTermsContextId = $context->getContextId();
					break;
				}
			}
		}

		$commentDao =& DAORegistry::getDAO('CommentDAO');
		$enableComments = $journal->getSetting('enableComments');

		if (($article->getEnableComments()) && ($enableComments == COMMENTS_AUTHENTICATED || $enableComments == COMMENTS_UNAUTHENTICATED || $enableComments == COMMENTS_ANONYMOUS)) {
			$comments =& $commentDao->getRootCommentsByArticleId($article->getId());
		}

		$galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
		if ($journal->getSetting('enablePublicGalleyId')) {
			$galley =& $galleyDao->getGalleyByBestGalleyId($galleyId, $article->getId());
		} else {
			$galley =& $galleyDao->getGalley($galleyId, $article->getId());
		}

		$templateMgr =& TemplateManager::getManager();

		if (!$galley) {
			// Get the subscription status if displaying the abstract;
			// if access is open, we can display links to the full text.
			import('issue.IssueAction');

			// The issue may not exist, if this is an editorial user
			// and scheduling hasn't been completed yet for the article.
			if ($issue) {
				$templateMgr->assign('subscriptionRequired', IssueAction::subscriptionRequired($issue));
			}

			$templateMgr->assign('subscribedUser', IssueAction::subscribedUser($journal, isset($issue) ? $issue->getId() : null, isset($article) ? $article->getId() : null));
			$templateMgr->assign('subscribedDomain', IssueAction::subscribedDomain($journal, isset($issue) ? $issue->getId() : null, isset($article) ? $article->getId() : null));

			$templateMgr->assign('showGalleyLinks', $journal->getSetting('showGalleyLinks'));

			import('payment.ojs.OJSPaymentManager');
			$paymentManager =& OJSPaymentManager::getManager();
			if ( $paymentManager->onlyPdfEnabled() ) {
				$templateMgr->assign('restrictOnlyPdf', true);
			}
			if ( $paymentManager->purchaseArticleEnabled() ) {
				$templateMgr->assign('purchaseArticleEnabled', true);
			}

			// Article cover page.
			$locale = Locale::getLocale();
			if (isset($article) && $article->getLocalizedFileName() && $article->getLocalizedShowCoverPage() && !$article->getLocalizedHideCoverPageAbstract($locale)) {
				import('file.PublicFileManager');
				$publicFileManager = new PublicFileManager();
				$coverPagePath = $request->getBaseUrl() . '/';
				$coverPagePath .= $publicFileManager->getJournalFilesPath($journal->getId()) . '/';
				$templateMgr->assign('coverPagePath', $coverPagePath);
				$templateMgr->assign('coverPageFileName', $article->getLocalizedFileName());
				$templateMgr->assign('width', $article->getLocalizedWidth());
				$templateMgr->assign('height', $article->getLocalized());
				$templateMgr->assign('coverPageAltText', $article->getLocalizedCoverPageAltText());
			}

			// Increment the published article's abstract views count
			if (!$request->isBot()) {
				$publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');
				$publishedArticleDao->incrementViewsByArticleId($article->getId());
			}
		} else {
			if (!$request->isBot()) {
				// Increment the galley's views count
				$galleyDao->incrementViews($galley->getId());
			}

			// Use the article's CSS file, if set.
			if ($galley->isHTMLGalley() && $styleFile =& $galley->getStyleFile()) {
				$templateMgr->addStyleSheet($router->url($request, null, 'article', 'viewFile', array(
					$article->getId(),
					$galley->getBestGalleyId($journal),
					$styleFile->getFileId()
				)));
			}
		}

		// Add font sizer js and css if not already in header
		$additionalHeadData = $templateMgr->get_template_vars('additionalHeadData');
		if (strpos(strtolower($additionalHeadData), 'sizer.js') === false) {
			$additionalHeadData .= $templateMgr->fetch('common/sizer.tpl');
			$templateMgr->assign('additionalHeadData', $additionalHeadData);
		}

		$templateMgr->assign_by_ref('issue', $issue);
		$templateMgr->assign_by_ref('article', $article);
		$templateMgr->assign_by_ref('galley', $galley);
		$templateMgr->assign_by_ref('section', $section);
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('postingAllowed', (
			($article->getEnableComments()) && (
			$enableComments == COMMENTS_UNAUTHENTICATED ||
			(($enableComments == COMMENTS_AUTHENTICATED ||
			$enableComments == COMMENTS_ANONYMOUS) &&
			Validation::isLoggedIn()))
		));
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign('defineTermsContextId', isset($defineTermsContextId)?$defineTermsContextId:null);
		$templateMgr->assign('comments', isset($comments)?$comments:null);

		$templateMgr->assign('sharingEnabled', $journalRt->getSharingEnabled());

		if($journalRt->getSharingEnabled()) {
			$templateMgr->assign('sharingRequestURL', $request->getRequestURL());
			$templateMgr->assign('sharingArticleTitle', $article->getArticleTitle());
			$templateMgr->assign_by_ref('sharingUserName', $journalRt->getSharingUserName());
			$templateMgr->assign_by_ref('sharingButtonStyle', $journalRt->getSharingButtonStyle());
			$templateMgr->assign_by_ref('sharingDropDownMenu', $journalRt->getSharingDropDownMenu());
			$templateMgr->assign_by_ref('sharingBrand', $journalRt->getSharingBrand());
			$templateMgr->assign_by_ref('sharingDropDown', $journalRt->getSharingDropDown());
			$templateMgr->assign_by_ref('sharingLanguage', $journalRt->getSharingLanguage());
			$templateMgr->assign_by_ref('sharingLogo', $journalRt->getSharingLogo());
			$templateMgr->assign_by_ref('sharingLogoBackground', $journalRt->getSharingLogoBackground());
			$templateMgr->assign_by_ref('sharingLogoColor', $journalRt->getSharingLogoColor());
			list($btnUrl, $btnWidth, $btnHeight) = SharingRT::sharingButtonImage($journalRt);
			$templateMgr->assign('sharingButtonUrl', $btnUrl);
			$templateMgr->assign('sharingButtonWidth', $btnWidth);
			$templateMgr->assign('sharingButtonHeight', $btnHeight);
		}
		
		if($journalRt->getAnnotationsEnabled()) {
		  $templateMgr->assign('annotationsEnabled', $journalRt->getAnnotationsEnabled());
		}

		$templateMgr->display('article/article.tpl');
	}

	/**
	 * Article Reading tools
	 * @param $args array
	 * @param $request Request
	 */
	function viewRST($args, &$request) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$galleyId = isset($args[1]) ? $args[1] : 0;

		$this->validate($request, $articleId, $galleyId);
		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;
		$this->setupTemplate();

		$rtDao =& DAORegistry::getDAO('RTDAO');
		$journalRt = $rtDao->getJournalRTByJournal($journal);

		// The RST needs to know whether this galley is HTML or not. Fetch the galley.
		$galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
		if ($journal->getSetting('enablePublicGalleyId')) {
			$galley =& $galleyDao->getGalleyByBestGalleyId($galleyId, $article->getId());
		} else {
			$galley =& $galleyDao->getGalley($galleyId, $article->getId());
		}

		$sectionDao =& DAORegistry::getDAO('SectionDAO');
		$section =& $sectionDao->getSection($article->getSectionId());

		$templateMgr =& TemplateManager::getManager();
		$templateMgr->assign_by_ref('issue', $issue);
		$templateMgr->assign_by_ref('article', $article);
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign_by_ref('galley', $galley);
		$templateMgr->assign_by_ref('journal', $journal);
		$templateMgr->assign_by_ref('section', $section);

		$templateMgr->assign('articleSearchByOptions', array(
			'' => 'search.allFields',
			ARTICLE_SEARCH_AUTHOR => 'search.author',
			ARTICLE_SEARCH_TITLE => 'article.title',
			ARTICLE_SEARCH_ABSTRACT => 'search.abstract',
			ARTICLE_SEARCH_INDEX_TERMS => 'search.indexTerms',
			ARTICLE_SEARCH_GALLEY_FILE => 'search.fullText'
		));

		// Bring in comment constants.
		$commentDao =& DAORegistry::getDAO('CommentDAO');

		$enableComments = $journal->getSetting('enableComments');
		$templateMgr->assign('postingAllowed', (
			$article->getEnableComments() &&
			$enableComments != COMMENTS_DISABLED
		));

		$templateMgr->assign('postingDisabled', (
			($enableComments == COMMENTS_AUTHENTICATED ||
			$enableComments == COMMENTS_ANONYMOUS) &&
			!Validation::isLoggedIn())
		);

		$templateMgr->assign_by_ref('journalRt', $journalRt);
		if ($journalRt->getEnabled()) {
			$version = $rtDao->getVersion($journalRt->getVersion(), $journalRt->getJournalId());
			if ($version) {
				$templateMgr->assign_by_ref('version', $version);
			}
		}

		if($journalRt->getAnnotationsEnabled()) {
		  $templateMgr->assign('annotationsEnabled', $journalRt->getAnnotationsEnabled());
		}

		$templateMgr->display('rt/rt.tpl');
	}

	/**
	 * View a file (inlines file).
	 * @param $args array ($articleId, $galleyId, $fileId [optional])
	 * @param $request Request
	 */
	function viewFile($args, &$request) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$galleyId = isset($args[1]) ? $args[1] : 0;
		$fileId = isset($args[2]) ? (int) $args[2] : 0;

		$this->validate($request, $articleId, $galleyId);
		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;

		$galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
		if ($journal->getSetting('enablePublicGalleyId')) {
			$galley =& $galleyDao->getGalleyByBestGalleyId($galleyId, $article->getId());
		} else {
			$galley =& $galleyDao->getGalley($galleyId, $article->getId());
		}

		if (!$galley) $request->redirect(null, null, 'view', $articleId);

		if (!$fileId) {
			$galleyDao->incrementViews($galley->getId());
			$fileId = $galley->getFileId();
		} else {
			if (!$galley->isDependentFile($fileId)) {
				$request->redirect(null, null, 'view', $articleId);
			}
		}

		if (!HookRegistry::call('$this->viewFile', array(&$article, &$galley, &$fileId))) {
			import('submission.common.Action');
			Action::viewFile($article->getId(), $fileId);
		}
	}

	/**
	 * Downloads the document
	 * @param $args array
	 * @param $request Request
	 */
	function download($args, &$request) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$galleyId = isset($args[1]) ? $args[1] : 0;
		$this->validate($request, $articleId, $galleyId);
		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;

		$galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
		if ($journal->getSetting('enablePublicGalleyId')) {
			$galley =& $galleyDao->getGalleyByBestGalleyId($galleyId, $article->getId());
		} else {
			$galley =& $galleyDao->getGalley($galleyId, $article->getId());
		}
		if ($galley) $galleyDao->incrementViews($galley->getId());

		if ($article && $galley && !HookRegistry::call('$this->downloadFile', array(&$article, &$galley))) {
			import('file.ArticleFileManager');
			$articleFileManager = new ArticleFileManager($article->getId());
			$articleFileManager->downloadFile($galley->getFileId());
		}
	}

	/**
	 * Download a supplementary file
	 * @param $args array
	 * @param $request Request
	 */
	function downloadSuppFile($args, &$request) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$suppId = isset($args[1]) ? $args[1] : 0;
		$this->validate($request, $articleId);
		$journal =& $this->journal;
		$issue =& $this->issue;
		$article =& $this->article;

		$suppFileDao =& DAORegistry::getDAO('SuppFileDAO');
		if ($journal->getSetting('enablePublicSuppFileId')) {
			$suppFile =& $suppFileDao->getSuppFileByBestSuppFileId($article->getId(), $suppId);
		} else {
			$suppFile =& $suppFileDao->getSuppFile((int) $suppId, $article->getId());
		}

		if ($article && $suppFile) {
			import('file.ArticleFileManager');
			$articleFileManager = new ArticleFileManager($article->getId());
			if ($suppFile->isInlineable()) {
				$articleFileManager->viewFile($suppFile->getFileId());
			} else {
				$articleFileManager->downloadFile($suppFile->getFileId());
			}
		}
	}

	/**
	 * Validation
	 * @see lib/pkp/classes/handler/PKPHandler#validate()
	 * @param $request Request
	 * @param $articleId integer
	 * @param $galleyId integer
	 */
	function validate(&$request, $articleId, $galleyId = null) {
		$router =& $request->getRouter();
		parent::validate(null, $request);

		import('issue.IssueAction');

		$journal =& $router->getContext($request);
		$journalId = $journal->getId();
		$article = $publishedArticle = $issue = null;
		$user =& $request->getUser();
		$userId = $user?$user->getId():0;

		$publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');
		if ($journal->getSetting('enablePublicArticleId')) {
			$publishedArticle =& $publishedArticleDao->getPublishedArticleByBestArticleId($journalId, $articleId);
		} else {
			$publishedArticle =& $publishedArticleDao->getPublishedArticleByArticleId((int) $articleId, $journalId);
		}

		$issueDao =& DAORegistry::getDAO('IssueDAO');
		if (isset($publishedArticle)) {
			$issue =& $issueDao->getIssueByArticleId($publishedArticle->getId(), $journalId);
		} else {
			$articleDao =& DAORegistry::getDAO('ArticleDAO');
			$article =& $articleDao->getArticle((int) $articleId, $journalId);
		}

		// If this is an editorial user who can view unpublished/unscheduled
		// articles, bypass further validation. Likewise for its author.
		if (($article || $publishedArticle) && (($article && IssueAction::allowedPrePublicationAccess($journal, $article) || ($publishedArticle && IssueAction::allowedPrePublicationAccess($journal, $publishedArticle))))) {
			$this->journal =& $journal;
			$this->issue =& $issue;
			$this->article =& $publishedArticle?$publishedArticle:$article;
			return true;
		}

		// Make sure the reader has rights to view the article/issue.
		if ($issue && $issue->getPublished()) {
			$subscriptionRequired = IssueAction::subscriptionRequired($issue);
			$isSubscribedDomain = IssueAction::subscribedDomain($journal, $issue->getId(), $articleId);

			// Check if login is required for viewing.
			if (!$isSubscribedDomain && !Validation::isLoggedIn() && $journal->getSetting('restrictArticleAccess') && isset($galleyId) && $galleyId != 0) {
				Validation::redirectLogin();
			}

			// bypass all validation if subscription based on domain or ip is valid
			// or if the user is just requesting the abstract
			if ( (!$isSubscribedDomain && $subscriptionRequired) &&
			     (isset($galleyId) && $galleyId!=0) ) {

				// Subscription Access
				$subscribedUser = IssueAction::subscribedUser($journal, $issue->getId(), $articleId);

				if (!(!$subscriptionRequired || $publishedArticle->getAccessStatus() == ARTICLE_ACCESS_OPEN || $subscribedUser)) {
					// if payment information is enabled,
					import('payment.ojs.OJSPaymentManager');
					$paymentManager =& OJSPaymentManager::getManager();

					if ( $paymentManager->purchaseArticleEnabled() || $paymentManager->membershipEnabled() ) {
						/* if only pdf files are being restricted, then approve all non-pdf galleys
						 * and continue checking if it is a pdf galley */
						if ( $paymentManager->onlyPdfEnabled() ) {
							$galleyDAO =& DAORegistry::getDAO('ArticleGalleyDAO');
							if ($journal->getSetting('enablePublicGalleyId')) {
								$galley =& $galleyDAO->getGalley($galleyId, $articleId);
							} else {
								$galley =& $galleyDAO->getGalleyByBestGalleyId($galleyId, $articleId);
							}
							if ( $galley && !$galley->isPdfGalley() ) {
								$this->journal =& $journal;
								$this->issue =& $issue;
								$this->article =& $publishedArticle;
								return true;
							}
						}

						if (!Validation::isLoggedIn()) {
							Validation::redirectLogin("payment.loginRequired.forArticle");
						}

						/* if the article has been paid for then forget about everything else
						 * and just let them access the article */
						$completedPaymentDAO =& DAORegistry::getDAO('OJSCompletedPaymentDAO');
						$dateEndMembership = $user->getSetting('dateEndMembership', 0);
						if ( $completedPaymentDAO->hasPaidPerViewArticle($userId, $articleId)
							|| (!is_null($dateEndMembership) && $dateEndMembership > time()) ) {
							$this->journal =& $journal;
							$this->issue =& $issue;
							$this->article =& $publishedArticle;
							return true;
						} else {
							$queuedPayment =& $paymentManager->createQueuedPayment($journalId, PAYMENT_TYPE_PURCHASE_ARTICLE, $user->getId(), $articleId, $journal->getSetting('purchaseArticleFee'));
							$queuedPaymentId = $paymentManager->queuePayment($queuedPayment);

							$templateMgr =& TemplateManager::getManager();
							$paymentManager->displayPaymentForm($queuedPaymentId, $queuedPayment);
							exit;
						}
					}

					if (!isset($galleyId) || $galleyId) {
						if (!Validation::isLoggedIn()) {
							Validation::redirectLogin("reader.subscriptionRequiredLoginText");
						}
						$request->redirect(null, 'about', 'subscriptions');
					}
				}
			}
		} else {
			$request->redirect(null, 'index');
		}
		$this->journal =& $journal;
		$this->issue =& $issue;
		$this->article =& $publishedArticle;
		return true;
	}

	function setupTemplate() {
		parent::setupTemplate();
		Locale::requireComponents(array(LOCALE_COMPONENT_PKP_READER));
	}
}

?>
