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

/**
 * Rule to parse dates from the document.
 *
 * The time zone used will be the default time zone defined on the Transformer.
 *
 * @see Transformer::getDefaultDateTimeZone()
 */
class TimeRule extends ConfigurationSelectorRule
{
    const PROPERTY_TIME_TYPE_DEPRECATED = 'article.time_type';
    const PROPERTY_DATETIME_TYPE = 'article.datetype';
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

        $time_rule->withProperties(
            [
                self::PROPERTY_TIME,
                self::PROPERTY_DATETIME_TYPE
            ],
            $configuration
        );

        // Just for retrocompatibility - issue #172
        if (isset($configuration[self::PROPERTY_TIME_TYPE_DEPRECATED])) {
            $time_rule->type = $configuration[self::PROPERTY_TIME_TYPE_DEPRECATED];
        }

        return $time_rule;
    }

    public function apply($transformer, $header, $node)
    {
        $time_type = $this->getProperty(self::PROPERTY_DATETIME_TYPE, $node);
        if ($time_type) {
            $this->type = $time_type;
        }

        // Builds the image
        $time_string = $this->getProperty(self::PROPERTY_TIME, $node);
        if ($time_string) {
            $time = Time::create($this->type);
            $time->withDatetime(new \DateTime($time_string, $transformer->getDefaultDateTimeZone()));
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
