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
use Facebook\InstantArticles\Elements\Italic;

class ItalicRule extends ConfigurationSelectorRule
{
    public static function create()
    {
        return new ItalicRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function getContextClass()
    {
        return TextContainer::getClassName();
    }

    public function apply($transformer, $text_container, $element)
    {
        $bold = Italic::create();
        $text_container->appendText($bold);
        $transformer->transform($bold, $element);
        return $text_container;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
