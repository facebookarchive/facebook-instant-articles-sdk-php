<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class HeaderTest extends \PHPUnit_Framework_TestCase
{

    public function testHeaderEmpty()
    {
        $header = Header::create();
        $expected = '';
        $rendered = $header->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testCompleteHeader()
    {
        date_default_timezone_set('UTC');

        $inline =
            '<script>alert("test");</script>';
        $document = new \DOMDocument();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($inline);

        $header =
            Header::create()
                ->withTitle('Big Top Title')
                ->withSubTitle('Smaller SubTitle')
                ->withPublishTime(
                    Time::create(Time::PUBLISHED)
                        ->withDatetime(
                            \DateTime::createFromFormat(
                                'j-M-Y G:i:s',
                                '14-Aug-1984 19:30:00'
                            )
                        )
                )
                ->withModifyTime(
                    Time::create(Time::MODIFIED)
                        ->withDatetime(
                            \DateTime::createFromFormat(
                                'j-M-Y G:i:s',
                                '10-Feb-2016 10:00:00'
                            )
                        )
                )
                ->addAuthor(
                    Author::create()
                        ->withName('Author One')
                        ->withDescription('Passionate coder and mountain biker')
                )
                ->addAuthor(
                    Author::create()
                        ->withName('Author Two')
                        ->withDescription('Weend surfer with heavy weight coding skils')
                        ->withURL('http://facebook.com/author')
                )
                ->withKicker('Some kicker of this article')
                ->withCover(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                        ->withCaption(
                            Caption::create()
                                ->appendText('Some caption to the image')
                        )
                )
                ->addAd(
                    Ad::create()
                        ->withSource('http://foo.com')
                )
                ->addAd(
                    Ad::create()
                        ->withSource('http://foo.com')
                        ->withWidth(350)
                        ->withHeight(50)
                        ->enableDefaultForReuse()
                )
                ->addAd(
                    Ad::create()
                        ->withWidth(300)
                        ->withHeight(250)
                        ->enableDefaultForReuse()
                        ->withHTML($fragment)
                );

        $expected =
            '<header>'.
                '<figure>'.
                    '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                    '<figcaption>Some caption to the image</figcaption>'.
                '</figure>'.
                '<h1>Big Top Title</h1>'.
                '<h2>Smaller SubTitle</h2>'.
                '<time class="op-published" datetime="1984-08-14T19:30:00+00:00">August 14th, 7:30pm</time>'.
                '<time class="op-modified" datetime="2016-02-10T10:00:00+00:00">February 10th, 10:00am</time>'.
                '<address>'.
                    '<a>Author One</a>'.
                    'Passionate coder and mountain biker'.
                '</address>'.
                '<address>'.
                    '<a href="http://facebook.com/author" rel="facebook">Author Two</a>'.
                    'Weend surfer with heavy weight coding skils'.
                '</address>'.
                '<h3 class="op-kicker">Some kicker of this article</h3>'.
                '<section class="op-ad-template">'.
                    '<figure class="op-ad">'.
                        '<iframe src="http://foo.com"></iframe>'.
                    '</figure>'.
                    '<figure class="op-ad op-ad-default">'.
                        '<iframe src="http://foo.com" width="350" height="50"></iframe>'.
                    '</figure>'.
                    '<figure class="op-ad">'.
                        '<iframe width="300" height="250">'.
                            '<script>alert("test");</script>'.
                        '</iframe>'.
                    '</figure>'.
                '</section>'.
            '</header>';

        $rendered = $header->render();

        $this->assertEquals($expected, $rendered);
    }

    public function testHeaderWithSingleDefaultAd()
    {
        $header =
            Header::create()
                ->addAd(
                    Ad::create()
                        ->withSource('http://foo.com')
                        ->withWidth(350)
                        ->withHeight(50)
                        ->enableDefaultForReuse()
                );

        // It should not set op-ad-default
        $expected =
            '<header>'.
                '<figure class="op-ad">'.
                    '<iframe src="http://foo.com" width="350" height="50"></iframe>'.
                '</figure>'.
            '</header>';

        $rendered = $header->render();

        $this->assertEquals($expected, $rendered);
    }

    public function testHeaderWithTitles()
    {
        $header =
            Header::create()
                ->withTitle(
                    H1::create()
                        ->appendText('Big Top Title')
                )
                ->withSubTitle(
                    H2::create()
                        ->appendText('Smaller SubTitle')
                )
                ->withKicker(
                    H3::create()
                        ->appendText('Kicker')
                );

        $expected =
            '<header>'.
                '<h1>Big Top Title</h1>'.
                '<h2>Smaller SubTitle</h2>'.
                '<h3 class="op-kicker">Kicker</h3>'.
            '</header>';

        $rendered = $header->render();

        $this->assertEquals($expected, $rendered);
    }

    public function testHeaderWithTitlesFormatted()
    {
        $header =
            Header::create()
                ->withTitle(
                    H1::create()
                        ->appendText('Big Top Title ')
                        ->appendText(Bold::create()->appendText('in Bold'))
                )
                ->withSubTitle(
                    H2::create()
                        ->appendText('Smaller SubTitle ')
                        ->appendText(Bold::create()->appendText('in Bold'))
                )
                ->withKicker(
                    H3::create()
                        ->appendText('Kicker ')
                        ->appendText(Bold::create()->appendText('in Bold'))
                );

        $expected =
            '<header>'.
                '<h1>Big Top Title <b>in Bold</b></h1>'.
                '<h2>Smaller SubTitle <b>in Bold</b></h2>'.
                '<h3 class="op-kicker">Kicker <b>in Bold</b></h3>'.
            '</header>';

        $rendered = $header->render();

        $this->assertEquals($expected, $rendered);
    }
}
