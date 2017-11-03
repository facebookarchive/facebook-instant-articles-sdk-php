<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\Util\BaseHTMLTestCase;

class MapTest extends BaseHTMLTestCase
{
    public function testRenderEmpty()
    {
        $map = Map::create();

        $expected = '';

        $rendered = $map->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithGeotag()
    {
        $script = <<<'JSON'
{
    "type": "Feature",
    "geometry": {
        "type": "Point",
        "coordinates": [23.166667, 89.216667]
    },
    "properties": {
        "title": "Jessore, Bangladesh",
        "radius": 750000,
        "pivot": true,
        "style": "satellite"
    }
}
JSON;

        $map =
            Map::create()
                ->withGeoTag(GeoTag::create()->withScript($script));

        $expected =
        '<figure class="op-map">'.
            '<script type="application/json" class="op-geotag">'.
                $script.
            '</script>'.
        '</figure>';

        $rendered = $map->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithGeotagAndCaption()
    {
        $script = <<<'JSON'
{
    "type": "Feature",
    "geometry": {
        "type": "Point",
        "coordinates": [23.166667, 89.216667]
    },
    "properties": {
        "title": "Jessore, Bangladesh",
        "radius": 750000,
        "pivot": true,
        "style": "satellite"
    }
}
JSON;

        $map =
            Map::create()
                ->withGeoTag(GeoTag::create()->withScript($script))
                ->withCaption(
                    Caption::create()
                        ->withTitle('Title of Image caption')
                        ->withCredit('Some caption to the image')
                        ->withPosition(Caption::POSITION_BELOW)
                );

        $expected =
        '<figure class="op-map">'.
            '<script type="application/json" class="op-geotag">'.
                $script.
            '</script>'.
            '<figcaption class="op-vertical-below">'.
                '<h1>Title of Image caption</h1>'.
                '<cite>Some caption to the image</cite>'.
            '</figcaption>'.
        '</figure>';

        $rendered = $map->render();
        $this->assertEqualsHtml($expected, $rendered);
    }
}
