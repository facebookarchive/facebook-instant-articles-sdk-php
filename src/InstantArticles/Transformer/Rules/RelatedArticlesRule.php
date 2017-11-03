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
use Facebook\InstantArticles\Elements\InstantArticle;

class RelatedArticlesRule extends ConfigurationSelectorRule
{
    const PROPERTY_TITLE = 'related.title';

    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new RelatedArticlesRule();
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

    public function apply($transformer, $instant_article, $node)
    {
        $related_articles = RelatedArticles::create();

        $title = $this->getProperty(self::PROPERTY_TITLE, $node);
        if ($title) {
            $related_articles->withTitle($title);
        }
        $instant_article->addChild($related_articles);

        $transformer->transform($related_articles, $node);

        return $instant_article;
    }
}
