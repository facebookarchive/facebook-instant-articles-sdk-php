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

class PassThroughRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Element::getClassName();
    }

    public static function create()
    {
        return new PassThroughRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $context, $node)
    {
        $transformer->transform($context, $node);
        return $context;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
