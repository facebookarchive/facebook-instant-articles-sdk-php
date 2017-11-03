<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Validators;

use Facebook\InstantArticles\Elements\Ad;
use Facebook\InstantArticles\Elements\Analytics;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\SlideShow;
use Facebook\InstantArticles\Elements\InstantArticle;

/**
 * Test unit against InstantArticleValidator
 * @see InstantArticleValidator
 */
class InstantArticleValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantArticle()
    {
        $article =
            InstantArticle::create()
                // Warning 1 - Invalid canonicalURL
                ->withCanonicalUrl('')
                // Warning 2 - Invalid empty header
                ->withHeader(Header::create())
                // Paragraph1
                ->addChild(
                    Paragraph::create()
                        ->appendText('Some text to be within a paragraph for testing.')
                )

                // Warning 3 - Invalid paragraph
                ->addChild(Paragraph::create())

                // Warning 4 - Invalid paragraph
                ->addChild(Paragraph::create()->appendText(" \n \t "))

                ->addChild(
                    // Warning 5 - Invalid image without URL
                    Image::create()
                )

                ->addChild(
                    // Warning 6 - Invalid image with empty URL
                    Image::create()->withURL('')
                )

                // Slideshow
                ->addChild(
                    SlideShow::create()
                        ->addImage(
                            Image::create()
                                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                        )
                        ->addImage(
                            // Warning 7 - Invalid image with empty URL
                            Image::create()
                        )
                )

                // Ad
                ->addChild(Ad::create()->withSource('http://foo.com'))

                // Paragraph4
                ->addChild(
                    Paragraph::create()
                        ->appendText('Other text to be within a second paragraph for testing.')
                )

                // Warning 8 - Invalid Analytics with empty content/src
                ->addChild(
                    Analytics::create()
                )

                // Warning 9 - Invalid empty Footer
                ->withFooter(Footer::create());

        $expected =
            '<!doctype html>'.
            '<html>'.
                '<head>'.
                    '<link rel="canonical" href=""/>'.
                    '<meta charset="utf-8"/>'.
                    '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                    '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                    '<meta property="op:markup_version" content="v1.0"/>'.
                '</head>'.
                '<body>'.
                    '<article>'.
                        '<p>Some text to be within a paragraph for testing.</p>'.
                        '<figure class="op-slideshow">'.
                            '<figure>'.
                                '<img src="https://jpeg.org/images/jpegls-home.jpg"/>'.
                            '</figure>'.
                        '</figure>'.
                        '<figure class="op-ad">'.
                            '<iframe src="http://foo.com"></iframe>'.
                        '</figure>'.
                        '<p>Other text to be within a second paragraph for testing.</p>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $result = $article->render();
        $this->assertEquals($expected, $result);

        $warnings = InstantArticleValidator::check($article);
        $this->assertEquals(9, count($warnings));
    }

    public function testFooter()
    {
        $footer = Footer::create();
        $expected = '';
        $result = $footer->render();
        $this->assertEquals($expected, $result);

        $warnings = array();
        InstantArticleValidator::getReport(array($footer), $warnings);
        $this->assertEquals(1, count($warnings));
        $this->assertContains('Footer must have at least one of the', $warnings[0]->__toString());
    }
}
