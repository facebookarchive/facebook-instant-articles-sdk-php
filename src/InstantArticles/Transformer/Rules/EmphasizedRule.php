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
use Facebook\InstantArticles\Elements\Emphasized;

class EmphasizedRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return TextContainer::getClassName();
    }

    public static function create()
    {
        return new EmphasizedRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $text_container, $element)
    {
        $emphasized = Emphasized::create();
        $text_container->appendText($emphasized);
        $transformer->transform($emphasized, $element);
        return $text_container;
    }
}
