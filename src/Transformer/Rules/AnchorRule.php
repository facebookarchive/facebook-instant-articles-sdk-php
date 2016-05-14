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
use Facebook\InstantArticles\Elements\Anchor;

class AnchorRule extends ConfigurationSelectorRule
{
    const PROPERTY_ANCHOR_HREF = 'anchor.href';
    const PROPERTY_ANCHOR_REL = 'anchor.rel';

    public static function create()
    {
        return new AnchorRule();
    }

    public function getContextClass()
    {
        return TextContainer::getClassName();
    }

    public static function createFrom($configuration)
    {
        $anchor_rule = self::create();

        $anchor_rule->withSelector($configuration['selector']);
        $properties = $configuration['properties'];
        $anchor_rule->withProperties(
            [
                self::PROPERTY_ANCHOR_HREF,
                self::PROPERTY_ANCHOR_REL
            ],
            $properties
        );

        return $anchor_rule;
    }

    public function apply($transformer, $text_container, $element)
    {
        $anchor = Anchor::create();

        $url = $this->getProperty(self::PROPERTY_ANCHOR_HREF, $element);
        $rel = $this->getProperty(self::PROPERTY_ANCHOR_REL, $element);

        if ($url) {
            $anchor->withHref($url);
        }
        if ($rel) {
            $anchor->withRel($rel);
        }
        $text_container->appendText($anchor);
        $transformer->transform($anchor, $element);

        return $text_container;
    }

    /**
     * @param array $configuration
     */
    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
