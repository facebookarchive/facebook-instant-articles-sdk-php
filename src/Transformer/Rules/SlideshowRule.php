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
use Facebook\InstantArticles\Elements\Slideshow;

class SlideshowRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new SlideshowRule();
    }

    public static function createFrom($configuration)
    {
        $slideshow_rule = self::create();
        $slideshow_rule->withSelector($configuration['selector']);

        return $slideshow_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        // Builds the slideshow
        $slideshow = Slideshow::create();
        $instant_article->addChild($slideshow);

        $transformer->transform($slideshow, $node);

        return $instant_article;
    }
}
