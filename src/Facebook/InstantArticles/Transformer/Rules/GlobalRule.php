<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Author;
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class GlobalRule extends ConfigurationSelectorRule
{
    const PROPERTY_GLOBAL_AUTHOR_URL = 'author.url';
    const PROPERTY_GLOBAL_AUTHOR_NAME = 'author.name';
    const PROPERTY_GLOBAL_AUTHOR_DESCRIPTION = 'author.description';
    const PROPERTY_GLOBAL_AUTHOR_ROLE_CONTRIBUTION = 'author.role_contribution';
    const PROPERTY_GLOBAL_CANONICAL_URL = 'article.canonical';
    const PROPERTY_GLOBAL_TITLE = 'article.title';
    const PROPERTY_TIME_PUBLISHED = 'article.publish';
    const PROPERTY_GLOBAL_BODY = 'article.body';

    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new GlobalRule();
    }

    public static function createFrom($configuration)
    {
        $author_rule = GlobalRule::create();

        $author_rule->withSelector($configuration['selector']);
        $properties = $configuration['properties'];
        $author_rule->withProperties(
            [
                self::PROPERTY_GLOBAL_AUTHOR_URL,
                self::PROPERTY_GLOBAL_AUTHOR_NAME,
                self::PROPERTY_GLOBAL_AUTHOR_DESCRIPTION,
                self::PROPERTY_GLOBAL_AUTHOR_ROLE_CONTRIBUTION,
                self::PROPERTY_GLOBAL_CANONICAL_URL,
                self::PROPERTY_GLOBAL_TITLE,
                self::PROPERTY_TIME_PUBLISHED,
                self::PROPERTY_GLOBAL_BODY
            ],
            $properties
        );

        return $author_rule;
    }

    public function apply($transformer, $instantArticle, $node)
    {
        // Builds the author
        $authorUrl = $this->getProperty(self::PROPERTY_GLOBAL_AUTHOR_URL, $node);
        $authorName = $this->getProperty(self::PROPERTY_GLOBAL_AUTHOR_NAME, $node);
        $authorRoleContribution = $this->getProperty(self::PROPERTY_GLOBAL_AUTHOR_ROLE_CONTRIBUTION, $node);
        $authorDescription = $this->getProperty(self::PROPERTY_GLOBAL_AUTHOR_DESCRIPTION, $node);

        $header = $instantArticle->getHeader();
        if (!$header) {
            $header = Header::create();
            $instantArticle->withHeader($header);
        }

        if ($authorName) {
            $author = Author::create();
            $author->withName($authorName);
            $header->addAuthor($author);

            if ($authorRoleContribution) {
                $author->withRoleContribution($authorRoleContribution);
            }

            if ($authorDescription) {
                $author->withDescription($authorDescription);
            }

            if ($authorUrl) {
                $author->withURL($authorUrl);
            }
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_GLOBAL_AUTHOR_NAME,
                    $header,
                    $node,
                    $this
                )
            );
        }

        // Treats title
        $articleTitle = $this->getProperty(self::PROPERTY_GLOBAL_TITLE, $node);
        if ($articleTitle) {
            $header->withTitle($transformer->transform(H1::create(), $articleTitle));
        }

        // Treats Canonical URL
        $articleCanonicalUrl = $this->getProperty(self::PROPERTY_GLOBAL_CANONICAL_URL, $node);
        if ($articleCanonicalUrl) {
            $instantArticle->withCanonicalURL($articleCanonicalUrl);
        }

        $body = $this->getProperty(self::PROPERTY_GLOBAL_BODY, $node);
        $transformer->transform($instantArticle, $body);

        return $instantArticle;
    }
}
