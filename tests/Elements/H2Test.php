<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class H2Test extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $h2 = H2::create();

        $expected = '';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $h2 =
            H2::create()
                ->appendText('Sub title simple text.');

        $expected =
            '<h2>'.
                'Sub title simple text.'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithPosition()
    {
        $h2 =
            H2::create()
                ->appendText('Sub title simple text.')
                ->withPosition(Caption::POSITION_ABOVE);

        $expected =
            '<h2 class="op-vertical-above">'.
                'Sub title simple text.'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithTextAlign()
    {
        $h2 =
            H2::create()
                ->appendText('Sub title simple text.')
                ->withTextAlignment(Caption::ALIGN_LEFT);

        $expected =
            '<h2 class="op-left">'.
                'Sub title simple text.'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithPositionAndAlignment()
    {
        $h2 =
            H2::create()
                ->appendText('Sub title simple text.')
                ->withPosition(Caption::POSITION_ABOVE)
                ->withTextAlignment(Caption::ALIGN_LEFT);

        $expected =
            '<h2 class="op-vertical-above op-left">'.
                'Sub title simple text.'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithUnescapedHTML()
    {
        $h2 =
            H2::create()
                ->appendText(
                    '<b>Some</b> text to be <i>within</i> a <em>paragraph</em> for <strong>testing.</strong>'
                );

        $expected =
            '<h2>'.
                '&lt;b&gt;Some&lt;/b&gt; text to be &lt;i&gt;within&lt;/i&gt; a'.
                ' &lt;em&gt;paragraph&lt;/em&gt; for &lt;strong&gt;testing.&lt;/strong&gt;'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithFormattedText()
    {
        $h2 =
            H2::create()
                ->appendText(Bold::create()->appendText('Some'))
                ->appendText(' text to be ')
                ->appendText(Italic::create()->appendText('within'))
                ->appendText(' a ')
                ->appendText(Italic::create()->appendText('paragraph'))
                ->appendText(' for ')
                ->appendText(Bold::create()->appendText('testing.'));

        $expected =
            '<h2>'.
                '<b>Some</b> text to be <i>within</i> a <i>paragraph</i> for <b>testing.</b>'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLink()
    {
        $h2 =
            H2::create()
                ->appendText('Some ')
                ->appendText(
                    Anchor::create()
                        ->withHRef('http://foo.com')
                        ->appendText('link')
                )
                ->appendText('.');

        $expected =
            '<h2>'.
                'Some <a href="http://foo.com">link</a>.'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithNestedFormattedText()
    {
        $h2 =
            H2::create()
                ->appendText(
                    Bold::create()
                        ->appendText('Some ')
                        ->appendText(Italic::create()->appendText('nested formatting'))
                        ->appendText('.')
                );

        $expected =
            '<h2>'.
                '<b>Some <i>nested formatting</i>.</b>'.
            '</h2>';

        $rendered = $h2->render();
        $this->assertEquals($expected, $rendered);
    }
}
