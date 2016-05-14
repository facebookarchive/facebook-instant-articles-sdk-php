<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class AnalyticsTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $analytics =
            Analytics::create();

        $expected = '';

        $rendered = $analytics->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic()
    {
        $analytics =
            Analytics::create()
                ->withSource('http://foo.com');

        $expected =
            '<figure class="op-tracker">'.
                '<iframe src="http://foo.com"></iframe>'.
            '</figure>';

        $rendered = $analytics->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithHTML()
    {
        $inline =
            '<h1>Some custom code</h1>'.
            '<script>alert("test");</script>';
        $document = new \DOMDocument();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($inline);

        $analytics =
            Analytics::create()
                ->withHTML($fragment);

        $expected =
            '<figure class="op-tracker">'.
                '<iframe>'.
                    '<h1>Some custom code</h1>'.
                    '<script>alert("test");</script>'.
                '</iframe>'.
            '</figure>';

        $rendered = $analytics->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithGoogleAnalytics()
    {
        $google_analytics =
            '<!-- Google Analytics -->'.
            '<script>'.
                '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){'.
                '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'.
                'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)'.
                '})(window,document,"script","//www.google-analytics.com/analytics.js","ga");'.
                'ga("create", "UA-XXXXX-Y", "auto");'.
                'ga("send", "pageview");'.
            '</script>'.
            '<!-- End Google Analytics -->';
        $document = new \DOMDocument();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($google_analytics);

        $analytics =
            Analytics::create()
                ->withHTML($fragment);

        $expected =
            '<figure class="op-tracker">'.
                '<iframe>'.
                    $google_analytics.
                '</iframe>'.
            '</figure>';

        $rendered = $analytics->render();
        $this->assertEquals($expected, $rendered);
    }
}
