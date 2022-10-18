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

class InstantArticleTest extends BaseHTMLTestCase
{
    /**
     * @var InstantArticle
     */
    private $article;
    protected function setUp(): void
    {
        date_default_timezone_set('UTC');

        $inline =
            '<h1>Some custom code</h1>'.
            '<script>alert("test & more test");</script>';

        $this->article =
            InstantArticle::create()
                ->withCanonicalURL('http://foo.com/article.html')
                ->withStyle('myarticlestyle')
                ->withHeader(
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
                                ->withName('Author Name')
                                ->withDescription('Author more detailed description')
                        )
                        ->addAuthor(
                            Author::create()
                                ->withName('Author in FB')
                                ->withDescription('Author user in facebook')
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
                )
                // Paragraph1
                ->addChild(
                    Paragraph::create()
                        ->appendText('Some text to be within a paragraph for testing.')
                )

                // Paragraph2
                ->addChild(
                    Paragraph::create()
                        ->appendText('Other text to be within a second paragraph for testing.')
                )

                // Empty paragraph
                ->addChild(
                    Paragraph::create()
                )

                // Paragraph with only whitespace
                ->addChild(
                    Paragraph::create()
                        ->appendText(" \n \t ")
                )

                // Slideshow
                ->addChild(
                    Slideshow::create()
                        ->addImage(
                            Image::create()
                                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                        )
                        ->addImage(
                            Image::create()
                                ->withURL('https://jpeg.org/images/jpegls-home2.jpg')
                        )
                        ->addImage(
                            Image::create()
                                ->withURL('https://jpeg.org/images/jpegls-home3.jpg')
                        )
                )

                // Paragraph3
                ->addChild(
                    Paragraph::create()
                        ->appendText('Some text to be within a paragraph for testing.')
                )

                // Ad
                ->addChild(
                    Ad::create()
                        ->withSource('http://foo.com')
                )

                // Paragraph4
                ->addChild(
                    Paragraph::create()
                        ->appendText('Other text to be within a second paragraph for testing.')
                )

                // Analytics
                ->addChild(
                    Analytics::create()
                        ->withHTML($inline)
                )

                // Footer
                ->withFooter(
                    Footer::create()
                        ->withCredits('Some plaintext credits.')
                );
    }

    public function testRender()
    {

        $expected =
            '<!doctype html>'.
            '<html>'.
            '<head>'.
                '<link rel="canonical" href="http://foo.com/article.html"/>'.
                '<meta charset="utf-8"/>'.
                '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                '<meta property="op:markup_version" content="v1.0"/>'.
                '<meta property="fb:article_style" content="myarticlestyle"/>'.
            '</head>'.
            '<body>'.
                '<article>'.
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
                            '<a>Author Name</a>'.
                            'Author more detailed description'.
                        '</address>'.
                        '<address>'.
                            '<a href="http://facebook.com/author" rel="facebook">Author in FB</a>'.
                            'Author user in facebook'.
                        '</address>'.
                        '<h3 class="op-kicker">Some kicker of this article</h3>'.
                    '</header>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
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
                    '</figure>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<figure class="op-ad">'.
                        '<iframe src="http://foo.com"></iframe>'.
                    '</figure>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
                    '<figure class="op-tracker">'.
                        '<iframe>'.
                            '<h1>Some custom code</h1>'.
                            '<script>alert("test & more test");</script>'.
                        '</iframe>'.
                    '</figure>'.
                    '<footer>'.
                        '<aside>Some plaintext credits.</aside>'.
                    '</footer>'.
                '</article>'.
            '</body>'.
            '</html>';

        $this->assertEqualsHtml($expected, $this->article->render());
    }

    public function testRenderWithAds()
    {

        $expected =
            '<!doctype html>'.
            '<html>'.
            '<head>'.
                '<link rel="canonical" href="http://foo.com/article.html"/>'.
                '<meta charset="utf-8"/>'.
                '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                '<meta property="op:markup_version" content="v1.0"/>'.
                '<meta property="fb:use_automatic_ad_placement" content="enable=true ad_density=default"/>'.
                '<meta property="fb:article_style" content="myarticlestyle"/>'.
            '</head>'.
            '<body>'.
                '<article>'.
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
                            '<a>Author Name</a>'.
                            'Author more detailed description'.
                        '</address>'.
                        '<address>'.
                            '<a href="http://facebook.com/author" rel="facebook">Author in FB</a>'.
                            'Author user in facebook'.
                        '</address>'.
                        '<h3 class="op-kicker">Some kicker of this article</h3>'.
                        '<figure class="op-ad"><iframe src="http://foo.com"></iframe></figure>'.
                    '</header>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
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
                    '</figure>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<figure class="op-ad">'.
                        '<iframe src="http://foo.com"></iframe>'.
                    '</figure>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
                    '<figure class="op-tracker">'.
                        '<iframe>'.
                            '<h1>Some custom code</h1>'.
                            '<script>alert("test & more test");</script>'.
                        '</iframe>'.
                    '</figure>'.
                    '<footer>'.
                        '<aside>Some plaintext credits.</aside>'.
                    '</footer>'.
                '</article>'.
            '</body>'.
            '</html>';

        $this->article->getHeader()->addAd(Ad::create()->withSource('http://foo.com'));

        $this->assertEqualsHtml($expected, $this->article->render());
    }

    public function testRenderWithoutAds()
    {
        $article =
            InstantArticle::create()
                ->disableAutomaticAdPlacement()
                ->withHeader(
                    Header::create()
                        ->addAd(
                            Ad::create()
                        )
                );
        $result = $article->render();
        $expected =
            '<!doctype html>'.
            '<html>'.
                '<head>'.
                    '<link rel="canonical" href=""/>'.
                    '<meta charset="utf-8"/>'.
                    '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                    '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                    '<meta property="op:markup_version" content="v1.0"/>'.
                    '<meta property="fb:use_automatic_ad_placement" content="false"/>'.
                '</head>'.
                '<body>'.
                    '<article>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $this->assertEqualsHtml($expected, $result);
    }

    public function testInstantArticleAlmostEmpty()
    {
        $article =
            InstantArticle::create()
                ->withCanonicalURL('')
                ->withHeader(Header::create())
                // Paragraph1
                ->addChild(
                    Paragraph::create()
                        ->appendText('Some text to be within a paragraph for testing.')
                )

                // Empty paragraph
                ->addChild(
                    Paragraph::create()
                )

                // Paragraph with only whitespace
                ->addChild(
                    Paragraph::create()
                        ->appendText(" \n \t ")
                )

                ->addChild(
                    // Image without src
                    Image::create()
                )

                ->addChild(
                    // Image with empty src
                    Image::create()
                        ->withURL('')
                )

                // Slideshow
                ->addChild(
                    Slideshow::create()
                        ->addImage(
                            Image::create()
                                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                        )
                        ->addImage(
                            // Image without src URL for image
                            Image::create()
                        )
                )

                // Empty Ad
                ->addChild(
                    Ad::create()
                )

                // Paragraph4
                ->addChild(
                    Paragraph::create()
                        ->appendText('Other text to be within a second paragraph for testing.')
                )

                // Empty Analytics
                ->addChild(
                    Analytics::create()
                )

                // Empty Footer
                ->withFooter(
                    Footer::create()
                );

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
                        '<p>Other text to be within a second paragraph for testing.</p>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $result = $article->render();
        $this->assertEqualsHtml($expected, $result);
    }

    public function testIsValid()
    {
        $ia =
            InstantArticle::create()
                ->withCanonicalURL('http://wp.localtest.me/2016/04/12/stress-on-earth/')
                ->enableAutomaticAdPlacement()
                ->withHeader(
                    Header::create()
                        ->withTitle(
                            H1::create()->appendText('Peace on <b>earth</b>')
                        )
                        ->addAuthor(
                            Author::create()->withName('bill')
                        )
                        ->withPublishTime(
                            Time::create(Time::PUBLISHED)
                                ->withDatetime(
                                    \DateTime::createFromFormat(
                                        'j-M-Y G:i:s',
                                        '14-Aug-1984 19:30:00'
                                    )
                                )
                        )
                )
                ->addChild(
                    Paragraph::create()
                        ->appendText('Yes, peace is good for everybody!')
                        ->appendText(LineBreak::create())
                        ->appendText(' Man kind.')
                );
        $this->assertTrue($ia->isValid());
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf('Facebook\InstantArticles\Elements\InstantArticleInterface', $this->article);
    }

    public function testIsRTLEnabled()
    {
        $article =
            InstantArticle::create()
                ->withCanonicalURL('http://wp.localtest.me/2016/04/12/stress-on-earth/')
                ->enableAutomaticAdPlacement()
                ->enableRTL()
                ->withHeader(
                    Header::create()
                        ->withTitle(
                            H1::create()->appendText('Peace on <b>earth</b>')
                        )
                        ->addAuthor(
                            Author::create()->withName('bill')
                        )
                        ->withPublishTime(
                            Time::create(Time::PUBLISHED)
                                ->withDatetime(
                                    \DateTime::createFromFormat(
                                        'j-M-Y G:i:s',
                                        '14-Aug-1984 19:30:00'
                                    )
                                )
                        )
                )
                ->addChild(
                    Paragraph::create()
                        ->appendText('Yes, peace is good for everybody!')
                        ->appendText(LineBreak::create())
                        ->appendText(' Man kind.')
                );
        $result = $article->render();
        $expected =
            '<!doctype html>'.
            '<html dir="rtl">'.
                '<head>'.
                    '<link rel="canonical" href="http://wp.localtest.me/2016/04/12/stress-on-earth/"/>'.
                    '<meta charset="utf-8"/>'.
                    '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                    '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                    '<meta property="op:markup_version" content="v1.0"/>'.
                '</head>'.
                '<body>'.
                    '<article>'.
                        '<header>'.
                            '<h1>Peace on &lt;b&gt;earth&lt;/b&gt;</h1>'.
                            '<time class="op-published" datetime="1984-08-14T19:30:00+00:00">August 14th, 7:30pm</time>'.
                            '<address>'.
                                '<a>bill</a>'.
                            '</address>'.
                        '</header>'.
                        '<p>Yes, peace is good for everybody!<br/> Man kind.</p>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $this->assertEqualsHtml($expected, $result);
    }

    public function testGetFirstParagraph()
    {
        $article =
            InstantArticle::create()
                ->withCanonicalURL('http://wp.localtest.me/2016/04/12/stress-on-earth/')
                ->enableAutomaticAdPlacement()
                ->enableRTL()
                ->withHeader(
                    Header::create()
                        ->withTitle(
                            H1::create()->appendText('Peace on <b>earth</b>')
                        )
                        ->addAuthor(
                            Author::create()->withName('bill')
                        )
                        ->withPublishTime(
                            Time::create(Time::PUBLISHED)
                                ->withDatetime(
                                    \DateTime::createFromFormat(
                                        'j-M-Y G:i:s',
                                        '14-Aug-1984 19:30:00'
                                    )
                                )
                        )
                )
                ->addChild(
                    Paragraph::create()
                        ->appendText('Yes, peace is good for everybody!')
                        ->appendText(LineBreak::create())
                        ->appendText(' Man kind.')
                );
        $result = $article->getFirstParagraph()->render();
        $expected = '<p>Yes, peace is good for everybody!<br/> Man kind.</p>';

        $this->assertEqualsHtml($expected, $result);
    }

    public function testGetEmptyFirstParagraph()
    {
        $article =
            InstantArticle::create();
        $result = $article->getFirstParagraph()->render();
        $expected = '';

        $this->assertEqualsHtml($expected, $result);
    }

    public function testDeleteChildren()
    {
        $article =
            InstantArticle::create()
                ->withCanonicalURL('http://www.facebook-IA-test.com/category/test')
                ->enableAutomaticAdPlacement()
                ->withHeader(
                    Header::create()
                        ->withTitle(
                            H1::create()->appendText('A good test')
                        )
                        ->addAuthor(
                            Author::create()->withName('Dan')
                        )
                        ->withPublishTime(
                            Time::create(Time::PUBLISHED)
                                ->withDatetime(
                                    \DateTime::createFromFormat(
                                        'j-M-Y G:i:s',
                                        '09-Jan-2016 20:30:00'
                                    )
                                )
                        )
                )
                ->addChild(
                    Paragraph::create()
                        ->appendText('Just testing a deletion.')
                )
                ->addChild(
                    Paragraph::create()
                        ->appendText('This should not render afterwards.')
                );
        $article->deleteChild(1);
        $result = $article->render();

        $expected =
            '<!doctype html>'.
            '<html>'.
                '<head>'.
                    '<link rel="canonical" href="http://www.facebook-IA-test.com/category/test"/>'.
                    '<meta charset="utf-8"/>'.
                    '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                    '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                    '<meta property="op:markup_version" content="v1.0"/>'.
                '</head>'.
                '<body>'.
                    '<article>'.
                        '<header>'.
                            '<h1>A good test</h1>'.
                            '<time class="op-published" datetime="2016-01-09T20:30:00+00:00">January 9th, 8:30pm</time>'.
                            '<address>'.
                                '<a>Dan</a>'.
                            '</address>'.
                        '</header>'.
                        '<p>Just testing a deletion.</p>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $this->assertEqualsHtml($expected, $result);
    }

    public function testDeleteOnlyChild()
    {
        $article =
            InstantArticle::create()
                ->withCanonicalURL('http://www.facebook-IA-test.com/category/test')
                ->enableAutomaticAdPlacement()
                ->withHeader(
                    Header::create()
                        ->withTitle(
                            H1::create()->appendText('A good test')
                        )
                        ->addAuthor(
                            Author::create()->withName('Dan')
                        )
                        ->withPublishTime(
                            Time::create(Time::PUBLISHED)
                                ->withDatetime(
                                    \DateTime::createFromFormat(
                                        'j-M-Y G:i:s',
                                        '09-Jan-2016 20:30:00'
                                    )
                                )
                        )
                )
                ->addChild(
                    Paragraph::create()
                        ->appendText('Single paragraph to delete.')
                );
        $article->deleteChild(0);
        $result = $article->render();

        $expected =
            '<!doctype html>'.
            '<html>'.
                '<head>'.
                    '<link rel="canonical" href="http://www.facebook-IA-test.com/category/test"/>'.
                    '<meta charset="utf-8"/>'.
                    '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                    '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                    '<meta property="op:markup_version" content="v1.0"/>'.
                '</head>'.
                '<body>'.
                    '<article>'.
                        '<header>'.
                            '<h1>A good test</h1>'.
                            '<time class="op-published" datetime="2016-01-09T20:30:00+00:00">January 9th, 8:30pm</time>'.
                            '<address>'.
                                '<a>Dan</a>'.
                            '</address>'.
                        '</header>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $this->assertEqualsHtml($expected, $result);
    }

    public function testReplaceChildren()
    {
        $article =
            InstantArticle::create()
                ->withCanonicalURL('http://www.facebook-IA-test.com/category/test')
                ->enableAutomaticAdPlacement()
                ->withHeader(
                    Header::create()
                        ->withTitle(
                            H1::create()->appendText('A replacing test')
                        )
                        ->addAuthor(
                            Author::create()->withName('Dan')
                        )
                        ->withPublishTime(
                            Time::create(Time::PUBLISHED)
                                ->withDatetime(
                                    \DateTime::createFromFormat(
                                        'j-M-Y G:i:s',
                                        '09-Jan-2016 20:30:00'
                                    )
                                )
                        )
                )
                ->addChild(
                    Paragraph::create()
                        ->appendText('Ye olde body')
                );

        $newBody = array(
            Paragraph::create()
                ->appendText('The new body.'),
            Paragraph::create()
                ->appendText('With two paragraphs!')
            );

        $article->withChildren($newBody);
        $result = $article->render();

        $expected =
            '<!doctype html>'.
            '<html>'.
                '<head>'.
                    '<link rel="canonical" href="http://www.facebook-IA-test.com/category/test"/>'.
                    '<meta charset="utf-8"/>'.
                    '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                    '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                    '<meta property="op:markup_version" content="v1.0"/>'.
                '</head>'.
                '<body>'.
                    '<article>'.
                        '<header>'.
                            '<h1>A replacing test</h1>'.
                            '<time class="op-published" datetime="2016-01-09T20:30:00+00:00">January 9th, 8:30pm</time>'.
                            '<address>'.
                                '<a>Dan</a>'.
                            '</address>'.
                        '</header>'.
                        '<p>The new body.</p>'.
                        '<p>With two paragraphs!</p>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $this->assertEqualsHtml($expected, $result);
    }

    public function testRenderWithoutRecirculationAds()
    {

        $expected =
            '<!doctype html>'.
            '<html>'.
            '<head>'.
                '<link rel="canonical" href="http://foo.com/article.html"/>'.
                '<meta charset="utf-8"/>'.
                '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                '<meta property="op:markup_version" content="v1.0"/>'.
                '<meta property="fb:article_style" content="myarticlestyle"/>'.
            '</head>'.
            '<body>'.
                '<article>'.
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
                            '<a>Author Name</a>'.
                            'Author more detailed description'.
                        '</address>'.
                        '<address>'.
                            '<a href="http://facebook.com/author" rel="facebook">Author in FB</a>'.
                            'Author user in facebook'.
                        '</address>'.
                        '<h3 class="op-kicker">Some kicker of this article</h3>'.
                    '</header>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
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
                    '</figure>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<figure class="op-ad">'.
                        '<iframe src="http://foo.com"></iframe>'.
                    '</figure>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
                    '<figure class="op-tracker">'.
                        '<iframe>'.
                            '<h1>Some custom code</h1>'.
                            '<script>alert("test & more test");</script>'.
                        '</iframe>'.
                    '</figure>'.
                    '<footer>'.
                        '<aside>Some plaintext credits.</aside>'.
                    '</footer>'.
                '</article>'.
            '</body>'.
            '</html>';

        $this->article->disableAutomaticRecirculationPlacement();

        $this->assertEqualsHtml($expected, $this->article->render());
    }

    public function testRenderWithRecirculationAds()
    {

        $expected =
            '<!doctype html>'.
            '<html>'.
            '<head>'.
                '<link rel="canonical" href="http://foo.com/article.html"/>'.
                '<meta charset="utf-8"/>'.
                '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                '<meta property="op:generator:version" content="'.InstantArticle::CURRENT_VERSION.'"/>'.
                '<meta property="op:markup_version" content="v1.0"/>'.
                '<meta property="fb:op-recirculation-ads" content="placement_id=536990673154512_811481037959775"/>'.
                '<meta property="fb:article_style" content="myarticlestyle"/>'.
            '</head>'.
            '<body>'.
                '<article>'.
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
                            '<a>Author Name</a>'.
                            'Author more detailed description'.
                        '</address>'.
                        '<address>'.
                            '<a href="http://facebook.com/author" rel="facebook">Author in FB</a>'.
                            'Author user in facebook'.
                        '</address>'.
                        '<h3 class="op-kicker">Some kicker of this article</h3>'.
                    '</header>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
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
                    '</figure>'.
                    '<p>Some text to be within a paragraph for testing.</p>'.
                    '<figure class="op-ad">'.
                        '<iframe src="http://foo.com"></iframe>'.
                    '</figure>'.
                    '<p>Other text to be within a second paragraph for testing.</p>'.
                    '<figure class="op-tracker">'.
                        '<iframe>'.
                            '<h1>Some custom code</h1>'.
                            '<script>alert("test & more test");</script>'.
                        '</iframe>'.
                    '</figure>'.
                    '<footer>'.
                        '<aside>Some plaintext credits.</aside>'.
                    '</footer>'.
                '</article>'.
            '</body>'.
            '</html>';

        $this->article->enableAutomaticRecirculationPlacement();
        $this->article->withRecirculationPlacement('placement_id=536990673154512_811481037959775');

        $this->assertEqualsHtml($expected, $this->article->render());
    }
}
