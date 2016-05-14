<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class VideoTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $video = Video::create();

        $expected = '';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4');

        $expected =
            '<figure>'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithAttribution()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->withAttribution('Some example test attribution');

        $expected =
            '<figure>'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
                '<cite>'.
                    'Some example test attribution'.
                '</cite>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithPresentation()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->withPresentation(Video::FULLSCREEN);

        $expected =
            '<figure data-mode="'.Video::FULLSCREEN.'">'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithControls()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->enableControls();

        $expected =
            '<figure>'.
                '<video controls="controls">'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithoutAutoplay()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->disableAutoplay();

        $expected =
            '<figure>'.
                '<video data-fb-disable-autoplay="data-fb-disable-autoplay">'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithCaption()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the video')
                );

        $expected =
        '<figure>'.
            '<video>'.
                '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
            '</video>'.
            '<figcaption>Some caption to the video</figcaption>'.
        '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLike()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the video')
                )
                ->enableLike();

        $expected =
            '<figure data-feedback="fb:likes">'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
                '<figcaption>Some caption to the video</figcaption>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithComments()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the video')
                )
                ->enableComments();

        $expected =
            '<figure data-feedback="fb:comments">'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
                '<figcaption>Some caption to the video</figcaption>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLikeAndComments()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the video')
                )
                ->enableLike()
                ->enableComments();

        $expected =
            '<figure data-feedback="fb:likes,fb:comments">'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
                '<figcaption>Some caption to the video</figcaption>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithGeotag()
    {
        $geotag = <<<'JSON'
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

        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->withGeoTag(GeoTag::create()->withScript($geotag));

        $expected =
            '<figure>'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
                '<script type="application/json" class="op-geotag">'.
                    $geotag.
                '</script>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithFeedCover()
    {
        $video =
            Video::create()
                ->withURL('http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4')
                ->enableFeedCover();

        $expected =
            '<figure class="fb-feed-cover">'.
                '<video>'.
                    '<source src="http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4"/>'.
                '</video>'.
            '</figure>';

        $rendered = $video->render();
        $this->assertEquals($expected, $rendered);
    }
}
