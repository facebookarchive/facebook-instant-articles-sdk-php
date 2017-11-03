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
use Symfony\Component\CssSelector\CssSelectorConverter;

class ElementGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected $selector;

    public function createFrom($properties)
    {
        return $this->withSelector($properties['selector']);
    }

    /**
     * @param \DOMNode $node
     * @param string $selector
     * @return \DOMNodeList
     */
    public function findAll($node, $selector)
    {
        $domXPath = new \DOMXPath($node->ownerDocument);
        $converter = new CssSelectorConverter();
        $xpath = $converter->toXPath($selector);
        return $domXPath->query($xpath, $node);
    }

    /**
     * @param string $selector
     *
     * @return $this
     */
    public function withSelector($selector)
    {
        Type::enforce($selector, Type::STRING);
        $this->selector = $selector;

        return $this;
    }

    public function get($node)
    {
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements) && property_exists($elements, 'length') && $elements->length !== 0) {
            Transformer::markAsProcessed($elements->item(0));
            return Transformer::cloneNode($elements->item(0));
        }
        return null;
    }
}
