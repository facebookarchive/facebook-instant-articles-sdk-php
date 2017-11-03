<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

namespace Facebook\InstantArticles\Client;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\InstantArticleInterface;
use Facebook\InstantArticles\Validators\Type;

class Client
{
    const EDGE_NAME = '/instant_articles';

    /**
     * @var Facebook The main Facebook service client.
     */
    private $facebook;

    /**
     * @var int ID of the Facebook Page we are using for Instant Articles
     */
    protected $pageID;

    /**
     * @var bool|false Are we using the Instant Articles development sandbox?
     */
    protected $developmentMode = false;

    /**
     * @param Facebook $facebook the main Facebook service client
     * @param string $pageID Specify the Facebook Page to use for Instant Articles
     * @param bool $developmentMode|false Configure the service to use the Instant Articles development sandbox
     */
    public function __construct($facebook, $pageID, $developmentMode = false)
    {
        Type::enforce($facebook, 'Facebook\Facebook');
        Type::enforce($pageID, Type::STRING);
        Type::enforce($developmentMode, Type::BOOLEAN);

        // TODO throw if $facebook doesn't have a default_access_token
        $this->facebook = $facebook;
        $this->pageID = $pageID;
        $this->developmentMode = $developmentMode;
    }

    /**
     * Creates a client with a proper Facebook client instance.
     *
     * @param string $appID
     * @param string $appSecret
     * @param string $accessToken The page access token used to query the Facebook Graph API
     * @param string $pageID Specify the Facebook Page to use for Instant Articles
     * @param bool $developmentMode|false Configure the service to use the Instant Articles development sandbox
     *
     * @return static
     *
     * @throws FacebookSDKException
     */
    public static function create($appID, $appSecret, $accessToken, $pageID, $developmentMode = false)
    {
        Type::enforce($appID, Type::STRING);
        Type::enforce($appSecret, Type::STRING);
        Type::enforce($accessToken, Type::STRING);

        $facebook = new Facebook([
            'app_id' => $appID,
            'app_secret' => $appSecret,
            'default_access_token' => $accessToken,
            'default_graph_version' => 'v2.5'
        ]);

        return new static($facebook, $pageID, $developmentMode);
    }

    /**
     * Import an article into your Instant Articles library.
     *
     * @param InstantArticle $article The article to import
     * @param bool|false $published Specifies if this article should be taken live or not. Optional. Default: false.
     * @param bool|false $formatOutput Specifies if this article should be formatted after transformation. Optional. Default: false.
     *
     * @return int The submission status ID. It is not the article ID. (Since 1.3.0)
     */
    public function importArticle($article, $published = false, $forceRescrape = false, $formatOutput = false)
    {
        Type::enforce($article, 'Facebook\InstantArticles\Elements\InstantArticleInterface');
        Type::enforce($published, Type::BOOLEAN);

        // Never try to take live if we're in development (the API would throw an error if we tried)
        $published = $this->developmentMode ? false : $published;

        // Assume default access token is set on $this->facebook
        $response = $this->facebook->post($this->pageID . Client::EDGE_NAME, [
          'html_source' => $article->render(null, $formatOutput),
          'published' => $published,
          'development_mode' => $this->developmentMode,
        ]);

        if ($forceRescrape) {
            // Re-scrape Graph object for article URL
            $this->scrapeArticleURL($article->getCanonicalURL());
        }

        return $response->getGraphNode()->getField('id');
    }

    /**
     * Scrape Graph object for given URL
     *
     * @param string $canonicalURL The URL that will be scraped.
     */
    private function scrapeArticleURL($canonicalURL)
    {
        $this->facebook->post('/', [
            'id' => $canonicalURL,
            'scrape' => 'true',
        ]);
    }

    /**
     * Removes an article from your Instant Articles library.
     *
     * @param string $canonicalURL The canonical URL of the article to delete.
     *
     * @return InstantArticleStatus
     *
     * @todo Consider returning the \Facebook\FacebookResponse object sent by
     *   \Facebook\Facebook::delete(). For now we trust that if an Instant
     *   Article ID exists for the Canonical URL the delete operation will work.
     */
    public function removeArticle($canonicalURL)
    {
        if (!$canonicalURL) {
            return InstantArticleStatus::notFound(['$canonicalURL param not passed to ' . __FUNCTION__ . '.']);
        }

        Type::enforce($canonicalURL, Type::STRING);

        if ($articleID = $this->getArticleIDFromCanonicalURL($canonicalURL)) {
            $this->facebook->delete($articleID);
            return InstantArticleStatus::success();
        }
        return InstantArticleStatus::notFound([ServerMessage::info('An Instant Article ID ' . $articleID . ' was not found for ' . $canonicalURL . ' in ' . __FUNCTION__ . '.')]);
    }

    /**
     * Get an Instant Article ID on its canonical URL.
     *
     * @param string $canonicalURL The canonical URL of the article to get the status for.
     * @return int|null the article ID or null if not found
     */
    public function getArticleIDFromCanonicalURL($canonicalURL)
    {
        Type::enforce($canonicalURL, Type::STRING);

        $field = $this->developmentMode ? 'development_instant_article' : 'instant_article';

        $response = $this->facebook->get('?id=' . $canonicalURL . '&fields=' . $field);
        $instantArticle = $response->getGraphNode()->getField($field);

        if (!$instantArticle) {
            return null;
        }

        $articleID = $instantArticle->getField('id');
        return $articleID;
    }

    /**
     * Get the last submission status of an Instant Article.
     *
     * @param string|null $articleID the article ID
     * @return InstantArticleStatus
     */
    public function getLastSubmissionStatus($articleID)
    {
        if (!$articleID) {
            return InstantArticleStatus::notFound();
        }

        Type::enforce($articleID, Type::STRING);

        // Get the latest import status of this article
        $response = $this->facebook->get($articleID . '?fields=most_recent_import_status');
        $articleStatus = $response->getGraphNode()->getField('most_recent_import_status');

        $messages = [];
        if (isset($articleStatus['errors'])) {
            foreach ($articleStatus['errors'] as $error) {
                $messages[] = ServerMessage::fromLevel($error['level'], $error['message']);
            }
        }

        return InstantArticleStatus::fromStatus($articleStatus['status'], $messages);
    }

    /**
     * Get the submission status of an Instant Article.
     *
     * @param string|null $submissionStatusID the submission status ID
     * @return InstantArticleStatus
     */
    public function getSubmissionStatus($submissionStatusID)
    {
        if (!$submissionStatusID) {
            return InstantArticleStatus::notFound();
        }

        Type::enforce($submissionStatusID, Type::STRING);

        $response = $this->facebook->get($submissionStatusID . '?fields=status,errors');
        $articleStatus = $response->getGraphNode();

        $messages = [];
        $errors = $articleStatus->getField('errors');
        if (null !== $errors) {
            foreach ($errors as $error) {
                $messages[] = ServerMessage::fromLevel($error['level'], $error['message']);
            }
        }

        return InstantArticleStatus::fromStatus($articleStatus->getField('status'), $messages);
    }

    /**
     * Get the review submission status
     *
     * @return string The review status
     */
    public function getReviewSubmissionStatus()
    {
        $response = $this->facebook->get('me?fields=instant_articles_review_status');
        return $response->getGraphNode()->getField('instant_articles_review_status');
    }

    /**
     * Retrieve the article URLs already published on Instant Articles
     *
     * @return string[] The cannonical URLs from articles
     */
    public function getArticlesURLs($limit = 10, $development_mode = false)
    {
        $articleURLs = [];
        $response = $this->facebook->get(
            'me/instant_articles?fields=canonical_url&'.
            'development_mode='.($development_mode ? 'true' : 'false').
            '&limit='.$limit
        );
        $articles = $response->getGraphEdge();
        foreach ($articles as $article) {
            $articleURLs[] = $article['canonical_url'];
        }

        return $articleURLs;
    }

    /**
     * Claims an URL for the page
     *
     * @param string $url The root URL of the site
     */
    public function claimURL($url)
    {
        // Remove protocol from the URL
        $url = preg_replace('/^https?:\/\//i', '', $url);
        $response = $this->facebook->post($this->pageID . '/claimed_urls?url=' . urlencode($url));
        $node = $response->getGraphNode();
        $error = $node->getField('error');
        $success = $node->getField('success');
        if ($error) {
            throw new ClientException($error['error_user_msg']);
        }
        if (!$success) {
            throw new ClientException('Could not claim the URL');
        }
    }

    /**
     * Submits the page for review
     */
    public function submitForReview()
    {
        $response = $this->facebook->post($this->pageID . '/?instant_articles_submit_for_review=true');
        $node = $response->getGraphNode();
        $error = $node->getField('error');
        $success = $node->getField('success');
        if ($error) {
            throw new ClientException($error['error_user_msg']);
        }
        if (!$success) {
            throw new ClientException('Could not submit for review');
        }
    }
}
