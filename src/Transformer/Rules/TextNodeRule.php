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

class TextNodeRule extends Rule
{
    public function getContextClass()
    {
        return TextContainer::getClassName();
    }

    public static function create()
    {
        return new TextNodeRule();
    }

    public static function createFrom($configuration)
    {
        return self::create();
    }

    public function matchesContext($context)
    {
        if (is_a($context, $this->getContextClass())) {
            return true;
        }
        return false;
    }

    public function matchesNode($node)
    {
        if ($node->nodeName === '#text') {
            return true;
        }
        return false;
    }

    public function apply($transformer, $text_container, $text)
    {
        $text_container->appendText($text->textContent);
        return $text_container;
    }

    public function loadFrom($configuration)
    {
        // Nothing to load/configure
    }
}
