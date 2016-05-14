<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Paragraph;

class ParagraphRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new ParagraphRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $context_element, $element)
    {
        $p = Paragraph::create();
        $context_element->addChild($p);
        $transformer->transform($p, $element);

        return $context_element;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
