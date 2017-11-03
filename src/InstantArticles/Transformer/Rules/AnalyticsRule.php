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
use Facebook\InstantArticles\Elements\Analytics;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class AnalyticsRule extends ConfigurationSelectorRule
{
    const PROPERTY_TRACKER_URL = 'analytics.url';
    const PROPERTY_TRACKER_EMBED_URL = 'analytics.embed';

    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new AnalyticsRule();
    }

    public static function createFrom($configuration)
    {
        $analytics_rule = self::create();
        $analytics_rule->withSelector($configuration['selector']);

        $analytics_rule->withProperties(
            [
                self::PROPERTY_TRACKER_URL,
                self::PROPERTY_TRACKER_EMBED_URL
            ],
            $configuration
        );

        return $analytics_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $analytics = Analytics::create();

        // Builds the analytics
        $url = $this->getProperty(self::PROPERTY_TRACKER_URL, $node);
        if ($url) {
            $analytics->withSource($url);
        }

        $embed_code = $this->getProperty(self::PROPERTY_TRACKER_EMBED_URL, $node);
        if ($embed_code) {
            $analytics->withHTML($embed_code);
        }

        if ($url || $embed_code) {
            $instant_article->addChild($analytics);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    'embed code or url',
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        return $instant_article;
    }
}
