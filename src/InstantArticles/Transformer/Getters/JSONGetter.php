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

class JSONGetter extends ChildrenGetter
{
    /**
     * @var string
     */
    protected $attribute;

    public function createFrom($properties)
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function withAttribute($attribute)
    {
        Type::enforce($attribute, Type::STRING);
        $this->attribute = $attribute;

        return $this;
    }

    public function get($node)
    {
        $content = null;

        Type::enforce($node, 'DOMNode');
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            if ($this->attribute) {
                $content = $element->getAttribute($this->attribute);
            } else {
                $content = $element->textContent;
            }
        }

        if (!Type::isTextEmpty($content)) {
            return json_decode($content, true);
        }
        return null;
    }
}
