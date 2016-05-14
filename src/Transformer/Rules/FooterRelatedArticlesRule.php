<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\RelatedArticles;
use Facebook\InstantArticles\Elements\Footer;

class FooterRelatedArticlesRule extends ConfigurationSelectorRule
{
    const PROPERTY_TITLE = 'related.title';

    public function getContextClass()
    {
        return Footer::getClassName();
    }

    public static function create()
    {
        return new FooterRelatedArticlesRule();
    }

    public static function createFrom($configuration)
    {
        $related_articles_rule = self::create();
        $related_articles_rule->withSelector($configuration['selector']);

        $related_articles_rule->withProperty(
            self::PROPERTY_TITLE,
            self::retrieveProperty($configuration, self::PROPERTY_TITLE)
        );

        return $related_articles_rule;
    }

    public function apply($transformer, $footer, $node)
    {
        $related_articles = RelatedArticles::create();

        // Builds the image
        $title = $this->getProperty(self::PROPERTY_TITLE, $node);
        if ($title) {
            $related_articles->withTitle($title);
            $footer->withRelatedArticles($related_articles);
        }

        $transformer->transform($related_articles, $node);

        return $footer;
    }
}
