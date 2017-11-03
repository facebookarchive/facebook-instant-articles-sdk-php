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

class BlockquoteTest extends BaseHTMLTestCase
{
    public function testRenderEmpty()
    {
        $blockquote = Blockquote::create();

        $expected = '';

        $rendered = $blockquote->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $blockquote =
            Blockquote::create()
                ->appendText('Some text to be within a blockquote for testing.');

        $expected =
            '<blockquote>'.
                'Some text to be within a blockquote for testing.'.
            '</blockquote>';

        $rendered = $blockquote->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithBoldStrongItalicEm()
    {
        $blockquote =
            Blockquote::create()
                ->appendText(Bold::create()->appendText('Some'))
                ->appendText(' text to be ')
                ->appendText(Italic::create()->appendText('within'))
                ->appendText(' a ')
                ->appendText(Italic::create()->appendText('blockquote'))
                ->appendText(' for ')
                ->appendText(Bold::create()->appendText('testing.'));

        $expected =
            '<blockquote>'.
                '<b>Some</b> text to be <i>within</i> a <i>blockquote</i> for <b>testing.</b>'.
            '</blockquote>';

        $rendered = $blockquote->render();
        $this->assertEqualsHtml($expected, $rendered);
    }
}
