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
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\Map;
use Facebook\InstantArticles\Elements\GeoTag;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class GeoTagRule extends ConfigurationSelectorRule
{
    const PROPERTY_MAP_GEOTAG = 'map.geotag';

    public function getContextClass()
    {
        return [Image::getClassName(), Video::getClassName(), Map::getClassName()];
    }

    public static function create()
    {
        return new GeoTagRule();
    }

    public static function createFrom($configuration)
    {
        $geo_tag_rule = self::create();
        $geo_tag_rule->withSelector($configuration['selector']);

        $geo_tag_rule->withProperty(
            self::PROPERTY_MAP_GEOTAG,
            self::retrieveProperty($configuration, self::PROPERTY_MAP_GEOTAG)
        );

        return $geo_tag_rule;
    }

    public function apply($transformer, $media_container, $node)
    {
        $geo_tag = GeoTag::create();

        // Builds the image
        $script = $this->getProperty(self::PROPERTY_MAP_GEOTAG, $node);
        if ($script) {
            $geo_tag->withScript($script);
            $media_container->withGeoTag($geo_tag);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_MAP_GEOTAG,
                    $media_container,
                    $node,
                    $this
                )
            );
        }

        return $media_container;
    }
}
