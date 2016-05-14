<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class SocialEmbedTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $social_embed = SocialEmbed::create();

        $expected = '';

        $rendered = $social_embed->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $social_embed =
            SocialEmbed::create()
                ->withSource('http://foo.com');

        $expected =
            '<figure class="op-social">'.
                '<iframe src="http://foo.com"></iframe>'.
            '</figure>';

        $rendered = $social_embed->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasicWithCaption()
    {
        $social_embed =
            SocialEmbed::create()
                ->withSource('http://foo.com')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the embed')
                );

        $expected =
            '<figure class="op-social">'.
                '<iframe src="http://foo.com"></iframe>'.
                '<figcaption>Some caption to the embed</figcaption>'.
            '</figure>';

        $rendered = $social_embed->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderInline()
    {
        $inline =
            '<h1>Some custom code</h1>'.
            '<script>alert("test");</script>';
        $document = new \DOMDocument();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($inline);

        $social_embed =
            SocialEmbed::create()
                ->withHTML($fragment);

        $expected =
            '<figure class="op-social">'.
                '<iframe>'.
                    '<h1>Some custom code</h1>'.
                    '<script>alert("test");</script>'.
                '</iframe>'.
            '</figure>';

        $rendered = $social_embed->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithWidthAndHeight()
    {
        $social_embed =
            SocialEmbed::create()
                ->withSource('http://foo.com')
                ->withWidth(640)
                ->withHeight(480);

        $expected =
            '<figure class="op-social">'.
                '<iframe src="http://foo.com" width="640" height="480"></iframe>'.
            '</figure>';

        $rendered = $social_embed->render();
        $this->assertEquals($expected, $rendered);
    }
}
