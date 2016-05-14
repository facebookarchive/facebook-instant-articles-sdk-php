<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class SlideshowTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $slideshow = SlideShow::create();

        $expected = '';

        $rendered = $slideshow->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $slideshow =
            SlideShow::create()
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home2.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home3.jpg'));

        $expected =
            '<figure class="op-slideshow">'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home2.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home3.jpg"/>'.
                '</figure>'.
            '</figure>';

        $rendered = $slideshow->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLikeAndComments()
    {
        $slideshow =
            SlideShow::create()
                ->addImage(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                        ->enableLike()
                )
                ->addImage(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home2.jpg')
                        ->enableComments()
                )
                ->addImage(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home3.jpg')
                        ->enableComments()
                        ->enableLike()
                );

        $expected =
            '<figure class="op-slideshow">'.
                '<figure data-feedback="fb:likes">'.
                    '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '</figure>'.
                '<figure data-feedback="fb:comments">'.
                    '<img src="https://jpeg.org/images/jpegls-home2.jpg"/>'.
                '</figure>'.
                '<figure data-feedback="fb:likes,fb:comments">'.
                    '<img src="https://jpeg.org/images/jpegls-home3.jpg"/>'.
                '</figure>'.
            '</figure>';

        $rendered = $slideshow->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithCaption()
    {
        $slideshow =
            SlideShow::create()
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home2.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home3.jpg'))
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the slideshow')
                );

        $expected =
            '<figure class="op-slideshow">'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home2.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home3.jpg"/>'.
                '</figure>'.
                '<figcaption>Some caption to the slideshow</figcaption>'.
            '</figure>';

        $rendered = $slideshow->render();
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

        $slideshow =
            SlideShow::create()
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home2.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home3.jpg'))
                ->withMapGeoTag($geotag);

        $expected =
            '<figure class="op-slideshow">'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home2.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home3.jpg"/>'.
                '</figure>'.
                '<script type="application/json" class="op-geotag">'.
                    $geotag.
                '</script>'.
            '</figure>';

        $rendered = $slideshow->render();
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

        $slideshow =
            SlideShow::create()
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home2.jpg'))
                ->addImage(Image::create()->withURL('https://jpeg.org/images/jpegls-home3.jpg'))
                ->withAudio($audio);

        $expected =
            '<figure class="op-slideshow">'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home2.jpg"/>'.
                '</figure>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home3.jpg"/>'.
                '</figure>'.
                $expected_audio.
            '</figure>';

        $rendered = $slideshow->render();
        $this->assertEquals($expected, $rendered);
    }
}
