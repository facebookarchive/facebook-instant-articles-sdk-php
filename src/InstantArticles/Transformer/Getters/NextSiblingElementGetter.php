<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class NextSiblingElementGetter extends ElementGetter
{
    protected $siblingSelector;

    /**
     * @param string $siblingSelector
     *
     * @return $this
     */
    public function withSiblingSelector($siblingSelector)
    {
        Type::enforce($siblingSelector, Type::STRING);
        $this->siblingSelector = $siblingSelector;

        return $this;
    }

    public function createFrom($properties)
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        if (isset($properties['sibling.selector'])) {
            $this->withSiblingSelector($properties['sibling.selector']);
        }

        return $this;
    }

    public function get($node)
    {
        Type::enforce($node, 'DOMNode');
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            do {
                $element = $element->nextSibling;
            } while ($element !== null && !Type::is($element, 'DOMElement'));

            if ($element && Type::is($element, 'DOMElement')) {
                if ($this->siblingSelector) {
                    $siblings = self::findAll($element, $this->siblingSelector);
                    if (!empty($siblings) && $siblings->item(0)) {
                        $siblingElement = $siblings->item(0);
                    } else {
                        // Returns null because sibling content doesn't match
                        return null;
                    }
                } else {
                    $siblingElement = $element;
                }
                Transformer::markAsProcessed($siblingElement);
                return Transformer::cloneNode($siblingElement);
            }
        }
        return null;
    }
}
