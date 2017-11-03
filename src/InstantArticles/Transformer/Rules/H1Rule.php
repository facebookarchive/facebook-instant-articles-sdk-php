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
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Validators\Type;

class H1Rule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return [Header::getClassName(), Caption::getClassName(), InstantArticle::getClassName()];
    }

    public static function create()
    {
        return new H1Rule();
    }

    public static function createFrom($configuration)
    {
        $h1_rule = self::create();
        $h1_rule->withSelector($configuration['selector']);

        $h1_rule->withProperties(
            [
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER,
                Caption::POSITION_ABOVE,

                Caption::ALIGN_LEFT,
                Caption::ALIGN_CENTER,
                Caption::ALIGN_RIGHT,

                Caption::SIZE_SMALL,
                Caption::SIZE_MEDIUM,
                Caption::SIZE_LARGE,
                Caption::SIZE_XLARGE
            ],
            $configuration
        );

        return $h1_rule;
    }

    public function apply($transformer, $context_element, $node)
    {
        $h1 = H1::create();
        if (Type::is($context_element, array(Header::getClassName(), Caption::getClassName()))) {
            $context_element->withTitle($h1);
        } elseif (Type::is($context_element, InstantArticle::getClassName())) {
            $context_element->addChild($h1);
        }

        if ($this->getProperty(Caption::POSITION_BELOW, $node)) {
            $h1->withPosition(Caption::POSITION_BELOW);
        }
        if ($this->getProperty(Caption::POSITION_CENTER, $node)) {
            $h1->withPosition(Caption::POSITION_CENTER);
        }
        if ($this->getProperty(Caption::POSITION_ABOVE, $node)) {
            $h1->withPosition(Caption::POSITION_ABOVE);
        }

        if ($this->getProperty(Caption::ALIGN_LEFT, $node)) {
            $h1->withTextAlignment(Caption::ALIGN_LEFT);
        }
        if ($this->getProperty(Caption::ALIGN_CENTER, $node)) {
            $h1->withTextAlignment(Caption::ALIGN_CENTER);
        }
        if ($this->getProperty(Caption::ALIGN_RIGHT, $node)) {
            $h1->withTextAlignment(Caption::ALIGN_RIGHT);
        }

        $transformer->transform($h1, $node);
        return $context_element;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
