<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\TextContainer;
use Facebook\InstantArticles\Elements\Bold;

class BoldRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return TextContainer::getClassName();
    }

    public static function create()
    {
        return new BoldRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $text_container, $element)
    {
        $bold = Bold::create();
        $text_container->appendText($bold);
        $transformer->transform($bold, $element);
        return $text_container;
    }
}
