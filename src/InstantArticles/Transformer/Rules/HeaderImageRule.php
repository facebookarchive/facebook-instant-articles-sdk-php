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
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class HeaderImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';

    public function getContextClass()
    {
        return Header::getClassName();
    }

    public static function create()
    {
        return new HeaderImageRule();
    }

    public static function createFrom($configuration)
    {
        $image_rule = self::create();
        $image_rule->withSelector($configuration['selector']);

        $image_rule->withProperties(
            [
                self::PROPERTY_IMAGE_URL
            ],
            $configuration
        );

        return $image_rule;
    }

    public function apply($transformer, $header, $node)
    {
        $image = Image::create();

        // Builds the image
        $url = $this->getProperty(self::PROPERTY_IMAGE_URL, $node);
        if ($url) {
            $image->withURL($url);
            $header->withCover($image);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IMAGE_URL,
                    $header,
                    $node,
                    $this
                )
            );
        }

        $suppress_warnings = $transformer->suppress_warnings;
        $transformer->suppress_warnings = true;
        $transformer->transform($image, $node);
        $transformer->suppress_warnings = $suppress_warnings;

        return $header;
    }
}
