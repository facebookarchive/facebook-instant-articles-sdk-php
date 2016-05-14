<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Warnings;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Rules\SocialEmbedRule;

class InvalidSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidSelectorToString()
    {
        $json = <<<'JSON'
{
    "class": "SocialEmbedRule",
    "selector" : "figure.op-social",
    "properties" : {
        "socialembed.url" : {
            "type" : "string",
            "selector" : "iframe",
            "attribute": "src"
        },
        "socialembed.iframe" : {
            "type" : "children",
            "selector" : "iframe"
        }
    }
}
JSON;

        $properties = json_decode($json, true);

        $instant_article = InstantArticle::create();
        $document = new \DOMDocument();
        $node = $document->createElement('figcaption');
        $rule = SocialEmbedRule::createFrom($properties);

        $warning = new InvalidSelector('field a and b', $instant_article, $node, $rule);

        $result = $warning->__toString();
        $expected = 'Invalid selector for fields (field a and b). '.
            'The node being transformed was <figcaption> in the context of'.
            ' InstantArticle within the Rule SocialEmbedRule with these'.
            ' properties: { socialembed.url=StringGetter}';

        $this->assertEquals($expected, $result);
    }
}
