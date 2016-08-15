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

use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Transformer\Rules\BoldRule;
use Facebook\InstantArticles\Transformer\Rules\H1Rule;
use Facebook\InstantArticles\Transformer\Rules\ItalicRule;
use Facebook\InstantArticles\Transformer\Rules\ParagraphRule;
use Facebook\InstantArticles\Transformer\Rules\TextNodeRule;

class TransformerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        \Logger::configure(
            [
                'rootLogger' => [
                    'appenders' => ['facebook-instantarticles-transformer']
                ],
                'appenders' => [
                    'facebook-instantarticles-transformer' => [
                        'class' => 'LoggerAppenderConsole',
                        'threshold' => 'INFO',
                        'layout' => [
                            'class' => 'LoggerLayoutSimple'
                        ]
                    ]
                ]
            ]
        );
    }

    public function testSelfTransformerContent()
    {
        $json_file = file_get_contents('src/Facebook/InstantArticles/Parser/instant-articles-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/instant-article-example.html');

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($html_file);
        libxml_use_internal_errors(false);

        $transformer->transform($instant_article, $document);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";

        //var_dump($result);
        // print_r($warnings);
        $this->assertEquals($html_file, $result);
    }

    public function testSelfTransformerMultibyteContent()
    {
        $json_file = file_get_contents('src/Facebook/InstantArticles/Parser/instant-articles-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/instant-article-example-multibyte.html');

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($html_file);
        libxml_use_internal_errors(false);

        $transformer->transform($instant_article, $document);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";

        $this->assertEquals($html_file, $result);
    }

    public function testSelfTransformerNonUTF8Content()
    {
        $json_file = file_get_contents('src/Facebook/InstantArticles/Parser/instant-articles-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/instant-article-example-nonutf8.html');
        $utf8_file = mb_convert_encoding($html_file, 'utf-8', 'auto');

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($html_file);
        libxml_use_internal_errors(false);

        $transformer->transform($instant_article, $document);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";

        $this->assertEquals($utf8_file, $result);
    }

    public function testTransformerAddAndGetRules()
    {
        $transformer = new Transformer();
        $rule1 = new ParagraphRule();
        $rule2 = new ItalicRule();
        $transformer->addRule($rule1);
        $transformer->addRule($rule2);
        $this->assertEquals([$rule1, $rule2], $transformer->getRules());
    }

    public function testTransformerSetRules()
    {
        $transformer = new Transformer();
        $rule1 = new ParagraphRule();
        $rule2 = new ItalicRule();
        $transformer->setRules([$rule1, $rule2]);
        $this->assertEquals([$rule1, $rule2], $transformer->getRules());
    }

    public function testTransformerResetRules()
    {
        $transformer = new Transformer();
        $rule1 = new ParagraphRule();
        $rule2 = new ItalicRule();
        $transformer->addRule($rule1);
        $transformer->addRule($rule2);
        $transformer->resetRules();
        $this->assertEquals([], $transformer->getRules());
    }

    public function testTitleTransformedWithBold()
    {
        $transformer = new Transformer();
        $json_file = file_get_contents(__DIR__ . '/CMS/wp-rules.json');
        $transformer->loadRules($json_file);

        $title_html_string = '<?xml encoding="utf-8" ?><h1>Title <b>in bold</b></h1>';

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHtml($title_html_string);
        libxml_use_internal_errors(false);

        $header = Header::create();
        $transformer->transform($header, $document);

        $this->assertEquals('<h1>Title <b>in bold</b></h1>', $header->getTitle()->render());
    }
}
