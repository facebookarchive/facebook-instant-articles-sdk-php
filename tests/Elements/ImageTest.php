<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $image = Image::create();

        $expected = '';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg');

        $expected =
            '<figure>'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithCaption()
    {
        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the image')
                );

        $expected =
            '<figure>'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '<figcaption>Some caption to the image</figcaption>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithAttributionCaption()
    {
        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                ->withCaption(
                    Caption::create()
                        ->withTitle('Title of Image caption')
                        ->withCredit('Some caption to the image')
                        ->withPosition(Caption::POSITION_BELOW)
                );

        $expected =
            '<figure>'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '<figcaption class="op-vertical-below">'.
                    '<h1>Title of Image caption</h1>'.
                    '<cite>Some caption to the image</cite>'.
                '</figcaption>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLike()
    {
        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the image')
                )
                ->enableLike();

        $expected =
            '<figure data-feedback="fb:likes">'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '<figcaption>Some caption to the image</figcaption>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithComments()
    {
        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the image')
                )
                ->enableComments();

        $expected =
            '<figure data-feedback="fb:comments">'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '<figcaption>Some caption to the image</figcaption>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLikeAndComments()
    {
        $image =
          Image::create()
              ->withURL('https://jpeg.org/images/jpegls-home.jpg')
              ->withCaption(
                  Caption::create()
                      ->appendText('Some caption to the image')
              )
              ->enableLike()
              ->enableComments();

        $expected =
            '<figure data-feedback="fb:likes,fb:comments">'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '<figcaption>Some caption to the image</figcaption>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithFullscreen()
    {
        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                ->withPresentation(Image::FULLSCREEN);

        $expected =
            '<figure data-mode="fullscreen">'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
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

        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                ->withGeoTag(GeoTag::create()->withScript($script));

        $expected =
            '<figure>'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '<script type="application/json" class="op-geotag">'.
                    $script.
                '</script>'.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithAudio()
    {
        $audio =
            Audio::create()
                ->withURL('http://foo.com/mp3')
                ->withTitle('audio title')
                ->enableMuted()
                ->enableAutoplay();

        $expected_audio =
            '<audio title="audio title" autoplay="autoplay" muted="muted">'.
                '<source src="http://foo.com/mp3"/>'.
            '</audio>';

        $image =
            Image::create()
                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                ->withAudio($audio);

        $expected =
            '<figure>'.
                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                $expected_audio.
            '</figure>';

        $rendered = $image->render();
        $this->assertEquals($expected, $rendered);
    }
}
