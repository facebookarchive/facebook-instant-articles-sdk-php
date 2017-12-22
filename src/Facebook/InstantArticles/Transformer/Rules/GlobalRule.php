<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Author;
use Facebook\InstantArticles\Elements\Time;
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Transformer\Transformer;

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
    const PROPERTY_GLOBAL_HEADER_IMAGE = 'image.url';

    public function getContextClass(): vec<string>
    {
        return vec[InstantArticle::getClassName()];
    }

    public static function create(): GlobalRule
    {
        return new GlobalRule();
    }

    public static function createFrom(dict<string, mixed> $configuration): GlobalRule
    {
        $rule = GlobalRule::create();

        $rule->withSelector(Type::mixedToString($configuration['selector']));
        $rule->withProperties(
            vec[
                self::PROPERTY_GLOBAL_AUTHOR_URL,
                self::PROPERTY_GLOBAL_AUTHOR_NAME,
                self::PROPERTY_GLOBAL_AUTHOR_DESCRIPTION,
                self::PROPERTY_GLOBAL_AUTHOR_ROLE_CONTRIBUTION,
                self::PROPERTY_GLOBAL_CANONICAL_URL,
                self::PROPERTY_GLOBAL_TITLE,
                self::PROPERTY_TIME_PUBLISHED,
                self::PROPERTY_GLOBAL_BODY,
                self::PROPERTY_GLOBAL_HEADER_IMAGE,
            ],
            $configuration
        );

        return $rule;
    }

    public function apply(Transformer $transformer, Element $instantArticle, \DOMNode $node): Element
    {
        if (!$node || !$node->hasChildNodes()) {
            return $instantArticle;
        }
        // Builds the author
        $authorUrl = $this->getPropertyString(self::PROPERTY_GLOBAL_AUTHOR_URL, $node);
        $authorName = $this->getPropertyString(self::PROPERTY_GLOBAL_AUTHOR_NAME, $node);
        $authorRoleContribution = $this->getPropertyString(self::PROPERTY_GLOBAL_AUTHOR_ROLE_CONTRIBUTION, $node);
        $authorDescription = $this->getPropertyString(self::PROPERTY_GLOBAL_AUTHOR_DESCRIPTION, $node);
        invariant($instantArticle instanceof InstantArticle, 'Error, $element is not a InstantArticle.');
        $header = $instantArticle->getHeader();
        if (!$header) {
            $header = Header::create();
            $instantArticle->withHeader($header);
        }

        if ($authorName !== null) {
            $author = Author::create();
            $author->withName($authorName);
            $header->addAuthor($author);

            if ($authorRoleContribution !== null) {
                $author->withRoleContribution($authorRoleContribution);
            }

            if ($authorDescription !== null) {
                $author->withDescription($authorDescription);
            }

            if ($authorUrl !== null) {
                $author->withURL($authorUrl);
            }
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_GLOBAL_AUTHOR_NAME,
                    $instantArticle,
                    $node,
                    $this
                )
            );
        }

        // Treats title
        $articleTitle = $this->getProperty(self::PROPERTY_GLOBAL_TITLE, $node);
        if ($articleTitle && $articleTitle instanceof \DOMNode) {
            $h1 = $transformer->transform(H1::create(), $articleTitle);
            invariant($h1 instanceof H1, 'Error, $h1 is not H1.');
            $header->withTitle($h1);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_GLOBAL_TITLE,
                    $instantArticle,
                    $node,
                    $this
                )
            );
        }

        // Treats Canonical URL
        $articleCanonicalUrl = $this->getPropertyString(self::PROPERTY_GLOBAL_CANONICAL_URL, $node);
        if ($articleCanonicalUrl !== null) {
            $instantArticle->withCanonicalUrl($articleCanonicalUrl);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_GLOBAL_CANONICAL_URL,
                    $instantArticle,
                    $node,
                    $this
                )
            );
        }

        // Treats Time Published
        $timePublished = $this->getProperty(self::PROPERTY_TIME_PUBLISHED, $node);
        if ($timePublished !== null) {
            invariant($timePublished instanceof \DateTime, 'Error $timePublished is not \DateTime.');
            $header->withTime(Time::create(Time::PUBLISHED)->withDatetime($timePublished));
        }

        // Treats Header Image
        $articleHeaderImageURL = $this->getPropertyString(self::PROPERTY_GLOBAL_HEADER_IMAGE, $node);
        if ($articleHeaderImageURL !== null) {
             $header->withCover(Image::create()->withURL($articleHeaderImageURL));
        }

        $body = $this->getProperty(self::PROPERTY_GLOBAL_BODY, $node);
        if ($body !== null && $body instanceof \DOMNode) {
            $transformer->transform($instantArticle, $body);
        }

        return $instantArticle;
    }
}
