<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Cite;

class CaptionCreditRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Caption::getClassName();
    }

    public static function create()
    {
        return new CaptionCreditRule();
    }

    public static function createFrom($configuration)
    {
        $cite_rule = self::create();
        $cite_rule->withSelector($configuration['selector']);

        $cite_rule->withProperties(
            [
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER,
                Caption::POSITION_ABOVE,

                Caption::ALIGN_LEFT,
                Caption::ALIGN_CENTER,
                Caption::ALIGN_RIGHT
            ],
            $configuration
        );

        return $cite_rule;
    }

    public function apply($transformer, $caption, $node)
    {
        $cite = Cite::create();
        $caption->withCredit($cite);

        if ($this->getProperty(Caption::POSITION_BELOW, $node)) {
            $cite->withPosition(Caption::POSITION_BELOW);
        }
        if ($this->getProperty(Caption::POSITION_CENTER, $node)) {
            $cite->withPosition(Caption::POSITION_CENTER);
        }
        if ($this->getProperty(Caption::POSITION_ABOVE, $node)) {
            $cite->withPosition(Caption::POSITION_ABOVE);
        }

        if ($this->getProperty(Caption::ALIGN_LEFT, $node)) {
            $cite->withTextAlignment(Caption::ALIGN_LEFT);
        }
        if ($this->getProperty(Caption::ALIGN_CENTER, $node)) {
            $cite->withTextAlignment(Caption::ALIGN_CENTER);
        }
        if ($this->getProperty(Caption::ALIGN_RIGHT, $node)) {
            $cite->withTextAlignment(Caption::ALIGN_RIGHT);
        }

        $transformer->transform($cite, $node);
        return $caption;
    }

    /**
     * @param array $configuration
     */
    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
