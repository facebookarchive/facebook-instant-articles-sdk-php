<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Client;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Authentication\AccessToken;
use Facebook\InstantArticles\Validators\Type;

class Helper
{
    /**
     * @var Facebook The main Facebook service client.
     */
    protected $facebook;

    /**
     * @param Facebook $facebook the main Facebook service client
     */
    public function __construct($facebook)
    {
        Type::enforce($facebook, 'Facebook\Facebook');

        // TODO throw if $facebook doesn't have a default_access_token
        $this->facebook = $facebook;
    }

    /**
     * Instantiates a new Helper object.
     *
     * @param string $appID
     * @param string $appSecret
     *
     * @return static
     *
     * @throws FacebookSDKException
     */
    public static function create($appID, $appSecret)
    {
        Type::enforce($appID, Type::STRING);
        Type::enforce($appSecret, Type::STRING);

        $facebook = new Facebook([
            'app_id' => $appID,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v2.5'
        ]);

        return new static($facebook);
    }

    /**
     * Returns the set of pages and their associated tokens based on a
     * short-lived user access token.
     *
     * @param AccessToken $accessToken A short-lived user access token.
     * @param int         $offset      Offset pages API results
     *
     * @return array
     *
     * @throws FacebookSDKException
     */
    public function getPagesAndTokens($accessToken, $offset = 0)
    {
        Type::enforce($accessToken, 'Facebook\Authentication\AccessToken');

        // If we don't have a long-lived user token, exchange for one
        if (! $accessToken->isLongLived()) {
            try {
                // The OAuth 2.0 client handler helps us manage access tokens
                $OAuth2Client = $this->facebook->getOAuth2Client();
                $accessToken = $OAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookResponseException $e) {
                throw new FacebookSDKException(
                    "Failed to exchange short-lived access token for long-lived access token."
                );
            }
        }

        // Set this access token as the default
        $this->facebook->setDefaultAccessToken($accessToken);

        // Request the list of pages and associated page tokens that are
        // connected to this user
        try {
            $response = $this->facebook->get('/me/accounts?fields=name,id,access_token,supports_instant_articles,picture&offset=' . $offset);
        } catch (FacebookResponseException $e) {
            throw new FacebookSDKException('Graph API returned an error: ' . $e->getMessage());
        }

        // Return the array of page objects for this user
        return $response->getGraphEdge();
    }
}
