<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Cite;
use Facebook\InstantArticles\Elements\Pullquote;

class PullquoteCiteRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Pullquote::getClassName();
    }

    public static function create()
    {
        return new PullquoteCiteRule();
    }

    public static function createFrom($configuration)
    {
        $cite_rule = self::create();
        $cite_rule->withSelector($configuration['selector']);

        return $cite_rule;
    }

    public function apply($transformer, $pullquote, $node)
    {
        $cite = Cite::create();
        $pullquote->withAttribution($cite);
        $transformer->transform($cite, $node);

        return $pullquote;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
