<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Time;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class TimeRule extends ConfigurationSelectorRule
{
    const PROPERTY_TIME_TYPE = 'article.time_type';
    const PROPERTY_TIME = 'article.time';

    private $type = Time::PUBLISHED;

    public function getContextClass()
    {
        return Header::getClassName();
    }

    public static function create()
    {
        return new TimeRule();
    }

    public static function createFrom($configuration)
    {
        $time_rule = self::create();
        $time_rule->withSelector($configuration['selector']);

        $time_rule->withProperty(
            self::PROPERTY_TIME,
            self::retrieveProperty($configuration, self::PROPERTY_TIME)
        );

        if (isset($configuration[self::PROPERTY_TIME_TYPE])) {
            $time_rule->type = $configuration[self::PROPERTY_TIME_TYPE];
        }

        return $time_rule;

    }

    public function apply($transformer, $header, $node)
    {
        // Builds the image
        $time_string = $this->getProperty(self::PROPERTY_TIME, $node);
        if ($time_string) {
            $time = Time::create($this->type);
            $time->withDatetime(new \DateTime($time_string));
            $header->withTime($time);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_TIME,
                    $header,
                    $node,
                    $this
                )
            );
        }

        return $header;
    }
}
