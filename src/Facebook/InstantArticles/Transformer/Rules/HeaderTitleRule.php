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
use Facebook\InstantArticles\Elements\H1;

class HeaderTitleRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Header::getClassName();
    }

    public static function create()
    {
        return new HeaderTitleRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $header, $h1)
    {
        $header->withTitle($transformer->transform(H1::create(), $h1));
        return $header;
    }
}
