<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class SlideshowImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';
    const PROPERTY_CAPTION_TITLE = 'caption.title';
    const PROPERTY_CAPTION_CREDIT = 'caption.credit';

    public function getContextClass()
    {
        return Slideshow::getClassName();
    }

    public static function create()
    {
        return new SlideshowImageRule();
    }

    public static function createFrom($configuration)
    {
        $image_rule = self::create();
        $image_rule->withSelector($configuration['selector']);

        $image_rule->withProperties(
            [
                self::PROPERTY_IMAGE_URL,
                self::PROPERTY_CAPTION_TITLE,
                self::PROPERTY_CAPTION_CREDIT
            ],
            $configuration
        );

        return $image_rule;
    }

    public function apply($transformer, $slideshow, $node)
    {
        $image = Image::create();

        // Builds the image
        $url = $this->getProperty(self::PROPERTY_IMAGE_URL, $node);
        if ($url) {
            $image->withURL($url);
            $slideshow->addImage($image);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IMAGE_URL,
                    $slideshow,
                    $node,
                    $this
                )
            );
        }

        $caption = Caption::create();

        $caption_title = $this->getProperty(self::PROPERTY_CAPTION_TITLE, $node);
        if ($caption_title) {
            $caption->withTitle($caption_title);
            $image->withCaption($caption);
        }

        $caption_credit = $this->getProperty(self::PROPERTY_CAPTION_CREDIT, $node);
        if ($caption_credit) {
            $caption->withCredit($caption_credit);
        }

        return $slideshow;
    }
}
