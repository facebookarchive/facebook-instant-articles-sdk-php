<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Parser;

use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\Util\BaseHTMLTestCase;

class ParserTest extends BaseHTMLTestCase
{
    public function testSelfParse()
    {
        $html_file = file_get_contents(__DIR__ . '/instant-article-example.html');

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($html_file);
        libxml_use_internal_errors(false);

        $parser = new Parser();
        $instant_article = $parser->parse($document);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";

        $this->assertEqualsHtml($html_file, $result);
    }

    public function testSelfParseString()
    {
        $html_file = file_get_contents(__DIR__ . '/instant-article-example.html');

        $parser = new Parser();
        $instant_article = $parser->parse($html_file);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";

        $this->assertEqualsHtml($html_file, $result);
    }

    public function testSelfParseStringNoTimezone()
    {
        $html_file_no_timezone = file_get_contents(__DIR__ . '/instant-article-example-no-timezone.html');
        $html_file_standard_timezone = file_get_contents(__DIR__ . '/instant-article-example-standard-timezone.html');

        $parser = new Parser();
        $instant_article = $parser->parse($html_file_no_timezone);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";

        $this->assertEqualsHtml($html_file_standard_timezone, $result);
    }

    public function testSelfParseStringNoTimezoneWithDefaultNYC()
    {
        $html_file_no_timezone = file_get_contents(__DIR__ . '/instant-article-example-no-timezone.html');
        $html_file_standard_timezone = file_get_contents(__DIR__ . '/instant-article-example-nyc-timezone.html');

        $transformer = new Transformer();
        $transformer->setDefaultDateTimeZone(new \DateTimeZone('America/New_York'));

        $parser = new Parser();
        $instant_article = $parser->parse($html_file_no_timezone, $transformer);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";

        $this->assertEqualsHtml($html_file_standard_timezone, $result);
    }

    public function testSelfParseAdPlacementOptions()
    {
        $html_file = file_get_contents(__DIR__ . '/instant-article-example-ads.html');

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($html_file);
        libxml_use_internal_errors(false);

        $parser = new Parser();
        $instant_article = $parser->parse($document);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');

        $result = $instant_article->render('', true)."\n";

        $this->assertEqualsHtml($html_file, $result);
    }
}
