<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class ParagraphTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $paragraph = Paragraph::create();

        $expected = '';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithUnescapedHTML()
    {
        $paragraph =
            Paragraph::create()
                ->appendText(
                    '<b>Some</b> text to be <i>within</i> a <em>paragraph</em> for <strong>testing.</strong>'
                );

        $expected =
            '<p>'.
                '&lt;b&gt;Some&lt;/b&gt; text to be &lt;i&gt;within&lt;/i&gt; '.
                'a &lt;em&gt;paragraph&lt;/em&gt; for &lt;strong&gt;testing.&lt;/strong&gt;'.
            '</p>';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithFormattedText()
    {
        $paragraph =
            Paragraph::create()
                ->appendText(Bold::create()->appendText('Some'))
                ->appendText(' text to be ')
                ->appendText(Italic::create()->appendText('within'))
                ->appendText(' a ')
                ->appendText(Italic::create()->appendText('paragraph'))
                ->appendText(' for ')
                ->appendText(Bold::create()->appendText('testing.'));

        $expected =
            '<p>'.
                '<b>Some</b> text to be <i>within</i> a <i>paragraph</i> for <b>testing.</b>'.
            '</p>';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLineBreak()
    {
        $paragraph =
            Paragraph::create()
                ->appendText('Some')
                ->appendText(LineBreak::create())
                ->appendText('line break');

        $expected =
            '<p>'.
                'Some'.
                '<br/>line break'.
            '</p>';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithLink()
    {
        $paragraph =
            Paragraph::create()
                ->appendText('Some ')
                ->appendText(
                    Anchor::create()
                        ->withHRef('http://foo.com')
                        ->appendText('link')
                )
                ->appendText('.');

        $expected =
            '<p>'.
                'Some <a href="http://foo.com">link</a>.'.
            '</p>';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderEmptyText()
    {
        $paragraph =
            Paragraph::create()
                ->appendText('');

        $expected = '';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderEmptyNBSP()
    {
        $paragraph =
            Paragraph::create()
                ->appendText('&nbsp;');

        $expected = '';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderEmptySpaces()
    {
        $paragraph =
            Paragraph::create()
                ->appendText('  ');

        $expected = '';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWhiteSpaceChars()
    {
        $paragraph =
            Paragraph::create()
                ->appendText("\t\n\r");

        $expected = '';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderEmptyFormatted()
    {
        $paragraph =
            Paragraph::create()
                ->appendText(Bold::create()->appendText('  '));

        $expected = '';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithNestedFormattedText()
    {
        $paragraph =
            Paragraph::create()
                ->appendText(
                    Bold::create()
                        ->appendText('Some ')
                        ->appendText(Italic::create()->appendText('nested formatting'))
                        ->appendText('.')
                );


        $expected =
            '<p>'.
                '<b>Some <i>nested formatting</i>.</b>'.
            '</p>';

        $rendered = $paragraph->render();
        $this->assertEquals($expected, $rendered);
    }
}
