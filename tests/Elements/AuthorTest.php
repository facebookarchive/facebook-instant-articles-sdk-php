<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class AuthorTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $author =
            Author::create();

        $expected = '';

        $rendered = $author->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderAuthorWithFB()
    {
        $author =
            Author::create()
                ->withURL('http://facebook.com/everton.rosario')
                ->withName('Everton Rosario')
                ->withDescription('Passionate coder and mountain biker');

        $expected =
            '<address>'.
                '<a href="http://facebook.com/everton.rosario" rel="facebook">Everton Rosario</a>'.
                'Passionate coder and mountain biker'.
            '</address>';

        $rendered = $author->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderAuthorWithTwitter()
    {
        $author =
            Author::create()
                ->withURL('http://twitter.com/evertonrosario')
                ->withName('Everton Rosario')
                ->withDescription('Passionate coder and mountain biker');

        $expected =
            '<address>'.
                '<a href="http://twitter.com/evertonrosario">Everton Rosario</a>'.
                'Passionate coder and mountain biker'.
            '</address>';

        $rendered = $author->render();
        $this->assertEquals($expected, $rendered);
    }
}
