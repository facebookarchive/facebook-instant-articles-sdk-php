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

class CaptionTest extends BaseHTMLTestCase
{
    public function testRenderEmpty()
    {
        $caption = Caption::create();

        $expected = '';

        $rendered = $caption->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $caption =
            Caption::create()
                ->appendText('Caption Title');

        $expected =
            '<figcaption>'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithSubTitle()
    {
        $caption =
            Caption::create()
                ->withTitle('Caption Title')
                ->withSubTitle('Caption SubTitle');

        $expected =
            '<figcaption>'.
                '<h1>Caption Title</h1>'.
                '<h2>Caption SubTitle</h2>'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithCredit()
    {
        $caption =
            Caption::create()
                ->withTitle('Caption Title')
                ->withCredit('Caption Credit');

        $expected =
            '<figcaption>'.
                '<h1>Caption Title</h1>'.
                '<cite>Caption Credit</cite>'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithPosition()
    {
        $caption =
            Caption::create()
                ->appendText('Caption Title')
                ->withPosition(Caption::POSITION_BELOW);

        $expected =
            '<figcaption class="op-vertical-below">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithLargeFontSize()
    {
        $caption =
            Caption::create()
                ->appendText('Caption Title')
                ->withFontsize(Caption::SIZE_LARGE);

        $expected =
            '<figcaption class="op-large">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithSmallFontSize()
    {
        $caption =
            Caption::create()
                ->appendText('Small Caption Title')
                ->withFontsize(Caption::SIZE_SMALL);

        $expected =
            '<figcaption class="op-small">'.
                'Small Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithTextAlignment()
    {
        $caption =
            Caption::create()
                ->appendText('Caption Title')
                ->withTextAlignment(Caption::ALIGN_LEFT);

        $expected =
            '<figcaption class="op-left">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithVerticalAlignment()
    {
        $caption =
          Caption::create()
            ->appendText('Caption Title')
            ->withVerticalAlignment(Caption::VERTICAL_BOTTOM);

        $expected =
          '<figcaption class="op-vertical-bottom">' .
          'Caption Title' .
          '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithAllFormating()
    {
        $caption =
            Caption::create()
                ->appendText('Caption Title')
                ->withFontsize(Caption::SIZE_LARGE)
                ->withPosition(Caption::POSITION_BELOW)
                ->withTextAlignment(Caption::ALIGN_LEFT)
                ->withVerticalAlignment(Caption::VERTICAL_BOTTOM);

        $expected =
            '<figcaption class="op-left op-vertical-bottom op-large op-vertical-below">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }
}
