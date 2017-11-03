<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Map;
use Facebook\InstantArticles\Elements\InstantArticle;

class MapRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new MapRule();
    }

    public static function createFrom($configuration)
    {
        $map_rule = self::create();
        $map_rule->withSelector($configuration['selector']);
        return $map_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $map = Map::create();
        $instant_article->addChild($map);
        $transformer->transform($map, $node);

        return $instant_article;
    }
}
