<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Sponsor;
use Facebook\InstantArticles\Validators\Type;

class SponsorRule extends ConfigurationSelectorRule
{
    const PROPERTY_SPONSOR_PAGE_URL = 'sponsor.page_url';

    public function getContextClass()
    {
        return Header::getClassName();
    }

    public static function create()
    {
        return new SponsorRule();
    }

    public static function createFrom($configuration)
    {
        $sponsor_rule = SponsorRule::create();

        $sponsor_rule->withSelector($configuration['selector']);
        $sponsor_rule->withProperty(
            self::PROPERTY_SPONSOR_PAGE_URL,
            self::retrieveProperty($configuration, self::PROPERTY_SPONSOR_PAGE_URL)
        );

        return $sponsor_rule;
    }

    public function apply($transformer, $header, $node)
    {
        $page_url = $this->getProperty(self::PROPERTY_SPONSOR_PAGE_URL, $node);
        if ($page_url && !Type::isTextEmpty($page_url)) {
            $sponsor = Sponsor::create();
            $header->withSponsor($sponsor);
            $sponsor->withPageUrl($page_url);
        }
        return $header;
    }
}
