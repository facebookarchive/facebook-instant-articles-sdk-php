<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class ListElementTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $list =
            ListElement::createOrdered();

        $expected = '';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderListItemsAllEmpty()
    {
        $list =
            ListElement::createOrdered()
                ->addItem('')
                ->addItem(' ')
                ->addItem("\t")
                ->addItem("\n \t");

        $expected = '';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderOrdered()
    {
        $list =
            ListElement::createOrdered()
                ->addItem('Item 1')
                ->addItem('Item 2')
                ->addItem('Item 3');

        $expected =
            '<ol>'.
                '<li>Item 1</li>'.
                '<li>Item 2</li>'.
                '<li>Item 3</li>'.
            '</ol>';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderOrderedWithAllItems()
    {
        $list =
            ListElement::createOrdered()
                ->withItems(['Item 1', 'Item 2', 'Item 3']);

        $expected =
            '<ol>'.
                '<li>Item 1</li>'.
                '<li>Item 2</li>'.
                '<li>Item 3</li>'.
            '</ol>';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderUnordered()
    {
        $list =
            ListElement::createUnordered()
                ->addItem('Item 1')
                ->addItem('Item 2')
                ->addItem('Item 3');

        $expected =
            '<ul>'.
                '<li>Item 1</li>'.
                '<li>Item 2</li>'.
                '<li>Item 3</li>'.
            '</ul>';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderUnorderedWithAllItems()
    {
        $list =
            ListElement::createUnordered()
                ->withItems(['Item 1', 'Item 2', 'Item 3']);

        $expected =
            '<ul>'.
                '<li>Item 1</li>'.
                '<li>Item 2</li>'.
                '<li>Item 3</li>'.
            '</ul>';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithSingleContainerUnordered()
    {
        $list =
            ListElement::createUnordered()
                ->addItem(ListItem::create()->appendText(Paragraph::create()->appendText('Item 1')))
                ->addItem(ListItem::create()->appendText(Div::create()->appendText('Item 2')))
                ->addItem(ListItem::create()->appendText(Span::create()->appendText('Item 3')));

        $expected =
            '<ul>'.
                '<li><p>Item 1</p></li>'.
                '<li><div>Item 2</div></li>'.
                '<li><span>Item 3</span></li>'.
            '</ul>';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }
}
