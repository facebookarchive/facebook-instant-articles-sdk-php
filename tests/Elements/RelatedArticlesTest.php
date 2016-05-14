<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class RelatedArticlesTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $element =
            RelatedArticles::create();

        $expected = '';

        $rendered = $element->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $element =
            RelatedArticles::create()
                ->addRelated(RelatedItem::create()->withURL('http://related.com/1'))
                ->addRelated(RelatedItem::create()->withURL('http://related.com/2'))
                ->addRelated(RelatedItem::create()->withURL('http://sponsored.com/1')->enableSponsored());

        $expected =
            '<ul class="op-related-articles">'.
                '<li><a href="http://related.com/1"></a></li>'.
                '<li><a href="http://related.com/2"></a></li>'.
                '<li data-sponsored="true"><a href="http://sponsored.com/1"></a></li>'.
            '</ul>';

        $rendered = $element->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithTitle()
    {
        $element =
            RelatedArticles::create()
                ->withTitle('Title for Related')
                ->addRelated(RelatedItem::create()->withURL('http://related.com/1'))
                ->addRelated(RelatedItem::create()->withURL('http://related.com/2'))
                ->addRelated(RelatedItem::create()->withURL('http://sponsored.com/1')->enableSponsored());

        $expected =
            '<ul class="op-related-articles" title="Title for Related">'.
                '<li><a href="http://related.com/1"></a></li>'.
                '<li><a href="http://related.com/2"></a></li>'.
                '<li data-sponsored="true"><a href="http://sponsored.com/1"></a></li>'.
            '</ul>';

        $rendered = $element->render();
        $this->assertEquals($expected, $rendered);
    }
}
