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

class MultipleElementsGetter extends AbstractGetter
{
    /**
     * @var Getters
     */
    protected $children = [];

    public function createFrom($properties)
    {
        foreach ($properties['children'] as $getter_configuration) {
            $this->children[] = GetterFactory::create($getter_configuration);
        }
        return $this;
    }

    public function get($node)
    {
        $fragment = $node->ownerDocument->createDocumentFragment();
        foreach ($this->children as $child) {
            $cloned_node = $child->get($node);
            if (Type::is($cloned_node, 'DOMNode')) {
                $fragment->appendChild($cloned_node);
            }
        }
        if ($fragment->hasChildNodes()) {
            return $fragment;
        }
        return null;
    }
}
