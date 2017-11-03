<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Map;
use Facebook\InstantArticles\Elements\Interactive;
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Elements\SocialEmbed;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;

class CaptionRule extends ConfigurationSelectorRule
{
    const PROPERTY_DEFAULT = 'caption.default';

    public function getContextClass()
    {
        return
            [
                Map::getClassName(),
                Image::getClassName(),
                Interactive::getClassName(),
                Slideshow::getClassName(),
                SocialEmbed::getClassName(),
                Video::getClassName()
            ];
    }

    public static function create()
    {
        return new CaptionRule();
    }

    public static function createFrom($configuration)
    {
        $caption_rule = self::create();
        $caption_rule->withSelector($configuration['selector']);

        $caption_rule->withProperties(
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
                Caption::SIZE_XLARGE,

                self::PROPERTY_DEFAULT
            ],
            $configuration
        );

        return $caption_rule;
    }

    public function apply($transformer, $container_of_caption, $node)
    {
        $caption = Caption::create();
        $container_of_caption->withCaption($caption);

        if ($this->getProperty(Caption::POSITION_BELOW, $node)) {
            $caption->withPosition(Caption::POSITION_BELOW);
        }
        if ($this->getProperty(Caption::POSITION_CENTER, $node)) {
            $caption->withPosition(Caption::POSITION_CENTER);
        }
        if ($this->getProperty(Caption::POSITION_ABOVE, $node)) {
            $caption->withPosition(Caption::POSITION_ABOVE);
        }

        if ($this->getProperty(Caption::ALIGN_LEFT, $node)) {
            $caption->withTextAlignment(Caption::ALIGN_LEFT);
        }
        if ($this->getProperty(Caption::ALIGN_CENTER, $node)) {
            $caption->withTextAlignment(Caption::ALIGN_CENTER);
        }
        if ($this->getProperty(Caption::ALIGN_RIGHT, $node)) {
            $caption->withTextAlignment(Caption::ALIGN_RIGHT);
        }

        if ($this->getProperty(Caption::SIZE_SMALL, $node)) {
            $caption->withFontsize(Caption::SIZE_SMALL);
        }
        if ($this->getProperty(Caption::SIZE_MEDIUM, $node)) {
            $caption->withFontsize(Caption::SIZE_MEDIUM);
        }
        if ($this->getProperty(Caption::SIZE_LARGE, $node)) {
            $caption->withFontsize(Caption::SIZE_LARGE);
        }
        if ($this->getProperty(Caption::SIZE_XLARGE, $node)) {
            $caption->withFontsize(Caption::SIZE_XLARGE);
        }

        $text_default = $this->getProperty(self::PROPERTY_DEFAULT, $node);
        if ($text_default) {
            $caption->withTitle($text_default);
        } else {
            $transformer->transform($caption, $node);
        }

        return $container_of_caption;
    }
}
