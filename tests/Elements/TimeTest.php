<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class TimeTest extends \PHPUnit_Framework_TestCase
{
    private $timeDate;

    protected function setUp()
    {
        date_default_timezone_set('UTC');
        $this->timeDate = \DateTime::createFromFormat(
            'j-M-Y G:i:s',
            '14-Aug-1984 19:30:00'
        );
    }

    public function testRenderEmpty()
    {
        $time = Time::create(Time::PUBLISHED);

        $expected = '';

        $rendered = $time->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $time =
            Time::create(Time::PUBLISHED)
                ->withDatetime($this->timeDate);

        $expected =
            '<time class="op-published" datetime="1984-08-14T19:30:00+00:00">'.
                'August 14th, 7:30pm'.
            '</time>';

        $rendered = $time->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderModified()
    {
        $time =
            Time::create(Time::PUBLISHED)
                ->withDatetime($this->timeDate);

        $expected =
            '<time class="op-published" datetime="1984-08-14T19:30:00+00:00">'.
                'August 14th, 7:30pm'.
            '</time>';

        $rendered = $time->render();
        $this->assertEquals($expected, $rendered);
    }
}
