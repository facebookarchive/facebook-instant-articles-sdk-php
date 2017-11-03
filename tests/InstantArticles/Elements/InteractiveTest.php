<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles;

use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Interactive;
use Facebook\Util\BaseHTMLTestCase;

class InteractiveTest extends BaseHTMLTestCase
{
    public function testRenderEmpty()
    {
        $interactive = Interactive::create();

        $expected = '';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
        $this->assertFalse($interactive->isValid());
    }

    public function testRenderBasic()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withWidth(640)
                ->withHeight(300);

        $expected =
            '<figure class="op-interactive">'.
                '<iframe src="http://foo.com/interactive-graphic" width="640" height="300"></iframe>'.
            '</figure>';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderBasicWithCaption()
    {
        $social_embed =
            interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withWidth(640)
                ->withHeight(300)
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the interactive graphic')
                );

        $expected =
            '<figure class="op-interactive">'.
                '<iframe src="http://foo.com/interactive-graphic" width="640" height="300"></iframe>'.
                '<figcaption>Some caption to the interactive graphic</figcaption>'.
            '</figure>';

        $rendered = $social_embed->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderBasicWithOnlyHeight()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withHeight(640);

        $expected = '';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
        $this->assertFalse($interactive->isValid());
    }

    public function testRenderBasicWithOnlyWidth()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withWidth(640);

        $expected = '';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
        $this->assertFalse($interactive->isValid());
    }

    public function testRenderBasicWithWidthHeight()
    {
        $interactive =
          Interactive::create()
            ->withSource('http://foo.com/interactive-graphic')
            ->withWidth(1600)
            ->withHeight(900);

        $expected =
          '<figure class="op-interactive">' .
              '<iframe src="http://foo.com/interactive-graphic" width="1600" height="900"></iframe>' .
          '</figure>';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderBasicWithOnlyColumnWidth()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withMargin(Interactive::COLUMN_WIDTH);

        $expected = '';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
        $this->assertFalse($interactive->isValid());
    }

    public function testRenderBasicWithOnlyNoMargin()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withMargin(Interactive::NO_MARGIN);

        $expected = '';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
        $this->assertFalse($interactive->isValid());
    }

    public function testRenderInlineWithHeightAndWidth()
    {
        $inline =
            '<h1>Some custom code</h1>'.
            '<script>alert("test & more test");</script>';

        $interactive =
            Interactive::create()
                ->withHTML($inline)
                ->withWidth(600)
                ->withHeight(640)
                ->withMargin(Interactive::NO_MARGIN);

        $expected =
            '<figure class="op-interactive">'.
                '<iframe class="no-margin" width="600" height="640">'.
                    '<h1>Some custom code</h1>'.
                    '<script>alert("test & more test");</script>'.
                '</iframe>'.
            '</figure>';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderInline()
    {
        $inline =
            '<h1>Some custom code</h1>'.
            '<script>alert("test & more test");</script>';

        $interactive =
            Interactive::create()
                ->withHTML($inline)
                ->withMargin(Interactive::NO_MARGIN);

        $expected =
            '<figure class="op-interactive">'.
                '<iframe class="no-margin">'.
                    '<h1>Some custom code</h1>'.
                    '<script>alert("test & more test");</script>'.
                '</iframe>'.
            '</figure>';

        $rendered = $interactive->render();
        $this->assertEqualsHtml($expected, $rendered);
        $this->assertTrue($interactive->isValid());
    }
}
