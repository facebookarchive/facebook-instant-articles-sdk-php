<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;

class IgnoreRule extends ConfigurationSelectorRule
{
    public static function create()
    {
        return new IgnoreRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function getContextClass()
    {
        return Element::getClassName();
    }

    public function apply($transformer, $context, $element)
    {
        return $context;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
