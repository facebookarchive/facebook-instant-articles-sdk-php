<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\InstantArticle;

class HeaderRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new HeaderRule();
    }

    public static function createFrom($configuration)
    {
        $header_rule = self::create();
        $header_rule->withSelector($configuration['selector']);
        return $header_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $header = Header::create();
        $instant_article->withHeader($header);
        $transformer->transform($header, $node);

        return $instant_article;
    }
}
