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

class StringGetter extends ChildrenGetter
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $suffix;

    public function createFrom($properties)
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        if (isset($properties['prefix'])) {
            $this->withPrefix($properties['prefix']);
        }
        if (isset($properties['suffix'])) {
            $this->withSuffix($properties['suffix']);
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

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function withPrefix($prefix)
    {
        Type::enforce($prefix, Type::STRING);
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function withSuffix($suffix)
    {
        Type::enforce($suffix, Type::STRING);
        $this->suffix = $suffix;

        return $this;
    }

    public function get($node)
    {
        Type::enforce($node, 'DOMNode');
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            if ($this->attribute) {
                $result = $element->getAttribute($this->attribute);
            } else {
                $result = $element->textContent;
            }

            if (!Type::isTextEmpty($this->prefix)) {
                $result = $this->prefix . $result;
            }
            if (!Type::isTextEmpty($this->suffix)) {
                $result = $result . $this->suffix;
            }
            return $result;
        }
        return null;
    }
}
