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

class ConstantGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected $value;

    public function createFrom($properties)
    {
        return $this->withValue($properties['value']);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function withValue($value)
    {
        Type::enforce($value, Type::STRING);
        $this->value = $value;

        return $this;
    }

    public function get($node)
    {
        return $this->value;
    }
}
