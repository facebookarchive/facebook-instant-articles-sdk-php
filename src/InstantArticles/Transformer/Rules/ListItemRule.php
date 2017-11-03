<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\ListElement;
use Facebook\InstantArticles\Elements\ListItem;

class ListItemRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return ListElement::getClassName();
    }

    public static function create()
    {
        return new ListItemRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $list, $element)
    {
        $li = ListItem::create();
        $list->addItem($li);
        $transformer->transform($li, $element);

        return $list;
    }
}
