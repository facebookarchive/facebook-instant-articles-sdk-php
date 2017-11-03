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
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Validators\Type;

class Parser
{

    /**
     * @param string|DOMDocument $document The document html of an Instant Article
     * @param Transformer $transformer The Transformer instance to use. A fresh one will be created by default.
     *
     * @return InstantArticle filled element that was parsed from the DOMDocument parameter
     */
    public function parse($content, $transformer = null)
    {
        if (Type::is($content, Type::STRING)) {
            libxml_use_internal_errors(true);
            $document = new \DOMDocument();
            $document->loadHTML($content);
            libxml_use_internal_errors(false);
        } else {
            $document = $content;
        }

        $json_file = file_get_contents(__DIR__ . '/instant-articles-rules.json');

        $instant_article = InstantArticle::create();

        if ($transformer === null) {
            $transformer = new Transformer();
        }
        $transformer->loadRules($json_file);
        $transformer->transform($instant_article, $document);

        return $instant_article;
    }
}
