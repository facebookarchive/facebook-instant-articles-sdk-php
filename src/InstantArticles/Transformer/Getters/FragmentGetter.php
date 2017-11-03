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

class FragmentGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected $fragment;

    public function createFrom($properties)
    {
        return $this->withFragment($properties['fragment']);
    }

    /**
     * @param string $fragment
     *
     * @return $this
     */
    public function withFragment($fragment)
    {
        Type::enforce($fragment, Type::STRING);
        $this->fragment = $fragment;

        return $this;
    }

    public function get($node)
    {
        $fragment = $node->ownerDocument->createDocumentFragment();
        $is_valid_markup = @$fragment->appendXML($this->fragment);
        if ($is_valid_markup) {
            return $fragment;
        }
        return null;
    }
}
