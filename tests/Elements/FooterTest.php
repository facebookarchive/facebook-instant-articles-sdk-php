<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class FooterTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $footer = Footer::create();

        $expected = '';

        $rendered = $footer->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $footer =
            Footer::create()
                ->withCredits('Some plaintext credits.');

        $expected =
            '<footer>'.
                '<aside>'.
                    'Some plaintext credits.'.
                '</aside>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithParagraphCredits()
    {
        $footer =
            Footer::create()
                ->withCredits([
                    Paragraph::create()->appendText('Some paragraph credits.'),
                    Paragraph::create()->appendText('Other paragraph credits.'),
                ]);

        $expected =
            '<footer>'.
                '<aside>'.
                    '<p>'.
                        'Some paragraph credits.'.
                    '</p>'.
                    '<p>'.
                        'Other paragraph credits.'.
                    '</p>'.
                '</aside>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithParagraphCreditsAppend()
    {
        $footer =
            Footer::create()
                ->addCredit(
                    Paragraph::create()->appendText('Some paragraph credits.')
                )
                ->addCredit(
                    Paragraph::create()->appendText('Other paragraph credits.')
                );

        $expected =
            '<footer>'.
                '<aside>'.
                    '<p>'.
                        'Some paragraph credits.'.
                    '</p>'.
                    '<p>'.
                        'Other paragraph credits.'.
                    '</p>'.
                '</aside>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithCopyright()
    {
        $footer =
            Footer::create()
                ->withCopyright('2016 Facebook');

        $expected =
            '<footer>'.
                '<small>'.
                    '2016 Facebook'.
                '</small>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithRelatedArticles()
    {
        $footer =
            Footer::create()
                ->withCopyright('2016 Facebook')
                ->withRelatedArticles(
                    RelatedArticles::create()
                        ->addRelated(RelatedItem::create()->withURL('http://related.com/1'))
                        ->addRelated(RelatedItem::create()->withURL('http://related.com/2'))
                        ->addRelated(RelatedItem::create()->withURL('http://sponsored.com/1')->enableSponsored())
                );

        $expected =
            '<footer>'.
                '<small>'.
                    '2016 Facebook'.
                '</small>'.
                '<ul class="op-related-articles">'.
                    '<li><a href="http://related.com/1"></a></li>'.
                    '<li><a href="http://related.com/2"></a></li>'.
                    '<li data-sponsored="true"><a href="http://sponsored.com/1"></a></li>'.
                '</ul>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEquals($expected, $rendered);
    }
}
