<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Transformer\GeneratorTrait;
use Facebook\Util\BaseHTMLTestCase;

class AdTest extends BaseHTMLTestCase
{
    use GeneratorTrait;

    public function testRenderEmpty()
    {
        $ad = Ad::create();

        $expected = '';

        $rendered = $ad->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $source = $this->getFaker()->url;

        $ad =
            Ad::create()
                ->withSource($source);

        $expected =
            '<figure class="op-ad">'.
                '<iframe src="'. $source . '"></iframe>'.
            '</figure>';

        $rendered = $ad->render();
        $this->assertEqualsHtml($expected, $rendered);
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
        $this->assertEqualsHtml($expected, $rendered);
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
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderInlineWithHeightAndWidth()
    {
        $inline =
            '<h1>Some custom code</h1>'.
            '<script>alert("test & more test");</script>';

        $ad =
            Ad::create()
                ->withHTML($inline)
                ->withHeight(640)
                ->withWidth(480);

        $expected =
            '<figure class="op-ad">'.
                '<iframe width="480" height="640">'.
                    '<h1>Some custom code</h1>'.
                    '<script>alert("test & more test");</script>'.
                '</iframe>'.
            '</figure>';

        $rendered = $ad->render();
        $this->assertEqualsHtml($expected, $rendered);
    }
}
