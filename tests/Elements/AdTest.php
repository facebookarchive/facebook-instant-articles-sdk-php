<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class AdTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $ad = Ad::create();

        $expected = '';

        $rendered = $ad->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $ad =
            Ad::create()
                ->withSource('http://foo.com');

        $expected =
            '<figure class="op-ad">'.
                '<iframe src="http://foo.com"></iframe>'.
            '</figure>';

        $rendered = $ad->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasicWithHeightAndWidth()
    {
        $ad =
            Ad::create()
                ->withSource('http://foo.com')
                ->withHeight(640)
                ->withWidth(480);

        $expected =
            '<figure class="op-ad">'.
                '<iframe src="http://foo.com" width="480" height="640"></iframe>'.
            '</figure>';

        $rendered = $ad->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasicWithDefaultEnabled()
    {
        $ad =
            Ad::create()
                ->withSource('http://foo.com')
                ->withHeight(640)
                ->withWidth(480)
                ->enableDefaultForReuse();

        $expected =
            '<figure class="op-ad op-ad-default">'.
                '<iframe src="http://foo.com" width="480" height="640"></iframe>'.
            '</figure>';

        $rendered = $ad->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderInlineWithHeightAndWidth()
    {
        $inline =
            '<h1>Some custom code</h1>'.
            '<script>alert("test");</script>';
        $document = new \DOMDocument();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($inline);
        $ad =
            Ad::create()
                ->withHTML($fragment)
                ->withHeight(640)
                ->withWidth(480);

        $expected =
            '<figure class="op-ad">'.
                '<iframe width="480" height="640">'.
                    '<h1>Some custom code</h1>'.
                    '<script>alert("test");</script>'.
                '</iframe>'.
            '</figure>';

        $rendered = $ad->render();
        $this->assertEquals($expected, $rendered);
    }
}
