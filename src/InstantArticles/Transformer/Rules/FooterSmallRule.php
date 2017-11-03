<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Elements\Small;

class FooterSmallRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Footer::getClassName();
    }

    public static function create()
    {
        return new FooterSmallRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $footer, $element)
    {
        $small = Small::create();
        $footer->withCopyright($small);
        $transformer->transform($small, $element);
        return $footer;
    }
}
