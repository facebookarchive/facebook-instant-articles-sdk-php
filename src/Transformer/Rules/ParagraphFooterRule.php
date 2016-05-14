<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\Footer;

class ParagraphFooterRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Footer::getClassName();
    }

    public static function create()
    {
        return new ParagraphFooterRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $footer, $element)
    {
        $p = Paragraph::create();
        $footer->addCredit($p);
        $transformer->transform($p, $element);
        return $footer;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
