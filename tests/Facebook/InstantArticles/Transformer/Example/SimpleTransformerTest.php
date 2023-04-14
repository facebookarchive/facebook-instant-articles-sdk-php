<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Logs\TransformerLog;
use Facebook\Util\BaseHTMLTestCase;

class SimpleTransformerTest extends BaseHTMLTestCase
{
    public function testSelfTransformerContent()
    {
        $json_file = file_get_contents(__DIR__ . '/simple-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/simple.html');

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($html_file);
        libxml_use_internal_errors(false);

        $transformer->transform($instant_article, $document);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";
        $expected = file_get_contents(__DIR__ . '/simple-ia.html');

        $this->assertEqualsHtml($expected, $result);
    }

    public function testDebugLog()
    {
        $expected = array(
            new TransformerLog(TransformerLog::INFO, 'Possible log levels: OFF, ERROR, INFO or DEBUG. To change it call method TransformerLog::setLevel("DEBUG").'),
            new TransformerLog(TransformerLog::INFO, 'Transformer initiated using encode [utf-8]')
        );
        $json_file = file_get_contents(__DIR__ . '/simple-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/simple.html');

        TransformerLog::setLevel(TransformerLog::DEBUG);
        $transformer->transformString($instant_article, $html_file);
        $result = array($transformer->getLogs()[0], $transformer->getLogs()[1]);

        $this->assertEqualsHtml($expected, $result);
    }

    public function testSelfTransformerContentMultipleAdsSettings()
    {
        $this->expectException( \Exception::class );
        $this->expectExceptionMessage( 'You must specify only one Ads Setting, either audience_network_placement_id or raw_html' );

        $json_file = file_get_contents(__DIR__ . '/simple-rules-multiple-ads-settings.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/simple.html');

        $transformer->transformString($instant_article, $html_file);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";
        $expected = file_get_contents(__DIR__ . '/simple-ia.html');

        $this->assertEqualsHtml($expected, $result);
    }

    public function testTransformerInvalidSyntaxRules()
    {
        $expected_error = new TransformerLog(TransformerLog::ERROR, 'Invalid JSON Rules: Syntax error, malformed JSON');
        $json_file = file_get_contents(__DIR__ . '/invalid-rules-syntax.json');

        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        TransformerLog::setLevel(TransformerLog::DEBUG);
        $result = $transformer->getLogs()[1];

        $this->assertEquals($expected_error, $result);
    }

    public function testTransformerUnexpectedControlCharacterRules()
    {
        // For PHP < 7
        $expected_error1 = new TransformerLog(TransformerLog::ERROR, 'Invalid JSON Rules: Syntax error, malformed JSON');

        // For PHP >= 7
        $expected_error2 = new TransformerLog(TransformerLog::ERROR, 'Invalid JSON Rules: Unexpected control character found');

        $json_file = file_get_contents(__DIR__ . '/invalid-rules-unexpected-character.json');

        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        TransformerLog::setLevel(TransformerLog::DEBUG);
        $result = $transformer->getLogs()[1];

        $this->assertEquals($expected_error1 == $result || $expected_error2 == $result, true);
    }

    public function testTransformerInvalidRuleClassName()
    {
        $expected_error = new TransformerLog(TransformerLog::ERROR, 'TextNodeRul was not found');
        $json_file = file_get_contents(__DIR__ . '/invalid-rule-class.json');
        $transformer = new Transformer();
        $transformer->loadRules($json_file);
        TransformerLog::setLevel(TransformerLog::DEBUG);
        $result = $transformer->getLogs()[1];
        $this->assertEquals($expected_error, $result);
    }
}
