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

class DateGetter extends StringGetter
{
    /**
     * @var string
     */
    protected $format;

    public function createFrom($properties)
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        if (isset($properties['format'])) {
            $this->withFormat($properties['format']);
        }
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function withFormat($format)
    {
        Type::enforce($format, Type::STRING);
        $this->format = $format;

        return $this;
    }

    public function get($node)
    {
        Type::enforce($node, 'DOMNode');
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);

            if ($this->attribute) {
                return \DateTime::createFromFormat($this->format, $element->getAttribute($this->attribute));
            }
            return \DateTime::createFromFormat($this->format, $element->textContent);
        }
        return null;
    }
}
