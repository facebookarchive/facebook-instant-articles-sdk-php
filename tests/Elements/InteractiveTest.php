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

class InteractiveTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $interactive = Interactive::create();

        $expected = '';

        $rendered = $interactive->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic');

        $expected =
            '<figure class="op-interactive">'.
                '<iframe src="http://foo.com/interactive-graphic"></iframe>'.
            '</figure>';

        $rendered = $interactive->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasicWithCaption()
    {
        $social_embed =
            interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withCaption(
                    Caption::create()
                        ->appendText('Some caption to the interactive graphic')
                );

        $expected =
            '<figure class="op-interactive">'.
                '<iframe src="http://foo.com/interactive-graphic"></iframe>'.
                '<figcaption>Some caption to the interactive graphic</figcaption>'.
            '</figure>';

        $rendered = $social_embed->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasicWithHeight()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withHeight(640);

        $expected =
            '<figure class="op-interactive">'.
                '<iframe src="http://foo.com/interactive-graphic" height="640"></iframe>'.
            '</figure>';

        $rendered = $interactive->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasicWithColumnWidth()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withWidth(Interactive::COLUMN_WIDTH);

        $expected =
            '<figure class="op-interactive">'.
                '<iframe src="http://foo.com/interactive-graphic" class="column-width"></iframe>'.
            '</figure>';

        $rendered = $interactive->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasicWithNoMargin()
    {
        $interactive =
            Interactive::create()
                ->withSource('http://foo.com/interactive-graphic')
                ->withWidth(Interactive::NO_MARGIN);

        $expected =
            '<figure class="op-interactive">'.
                '<iframe src="http://foo.com/interactive-graphic" class="no-margin"></iframe>'.
            '</figure>';

        $rendered = $interactive->render();
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

        $interactive =
            Interactive::create()
                ->withHTML($fragment)
                ->withHeight(640)
                ->withWidth(Interactive::NO_MARGIN);

        $expected =
            '<figure class="op-interactive">'.
                '<iframe class="no-margin" height="640">'.
                    '<h1>Some custom code</h1>'.
                    '<script>alert("test");</script>'.
                '</iframe>'.
            '</figure>';

        $rendered = $interactive->render();
        $this->assertEquals($expected, $rendered);
    }
}
