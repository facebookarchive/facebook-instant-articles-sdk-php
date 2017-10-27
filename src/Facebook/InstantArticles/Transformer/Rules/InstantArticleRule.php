<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class InstantArticleRule extends ConfigurationSelectorRule
{
    const PROPERTY_CANONICAL = 'article.canonical';
    const PROPERTY_CHARSET = 'article.charset';
    const PROPERTY_MARKUP_VERSION = 'article.markup.version';
    const PROPERTY_AUTO_AD_PLACEMENT = 'article.auto.ad';
    const PROPERTY_RECIRCULATION_AD_PLACEMENT = 'article.recirculation.ad';
    const PROPERTY_STYLE = 'article.style';

    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new InstantArticleRule();
    }

    public static function createFrom($configuration)
    {
        $canonical_rule = self::create();
        $canonical_rule->withSelector($configuration['selector']);

        $canonical_rule->withProperties(
            [
                self::PROPERTY_CANONICAL,
                self::PROPERTY_CHARSET,
                self::PROPERTY_MARKUP_VERSION,
                self::PROPERTY_AUTO_AD_PLACEMENT,
                self::PROPERTY_RECIRCULATION_AD_PLACEMENT,
                self::PROPERTY_STYLE
            ],
            $configuration
        );

        return $canonical_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        // Builds the image
        $url = $this->getProperty(self::PROPERTY_CANONICAL, $node);
        if ($url) {
            $instant_article->withCanonicalURL($url);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_CANONICAL,
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        $charset = $this->getProperty(self::PROPERTY_CHARSET, $node);
        if ($charset) {
            $instant_article->withCharset($charset);
        }

        $markup_version = $this->getProperty(self::PROPERTY_MARKUP_VERSION, $node);
        if ($markup_version) {
            //TODO Validate if the markup is valid with this code
        }

        $auto_ad_placement = $this->getProperty(self::PROPERTY_AUTO_AD_PLACEMENT, $node);
        if ($auto_ad_placement === 'false') {
            $instant_article->disableAutomaticAdPlacement();
        } else {
            $instant_article->enableAutomaticAdPlacement();
            $pairs = explode(' ', $auto_ad_placement, 2);
            if (count($pairs) === 2) {
                list($name, $value) = explode('=', $pairs[1], 2);
                $instant_article->withAdDensity($value);
            }
        }

        $recirculation_ad_placement = $this->getProperty(self::PROPERTY_RECIRCULATION_AD_PLACEMENT, $node);
        if (is_null($recirculation_ad_placement)) {
            $instant_article->disableAutomaticRecirculationPlacement();
        } else {
            $instant_article->enableAutomaticRecirculationPlacement();
            $instant_article->withRecirculationPlacement($recirculation_ad_placement);
        }

        $style = $this->getProperty(self::PROPERTY_STYLE, $node);
        if ($style) {
            $instant_article->withStyle($style);
        }

        return $instant_article;
    }
}
