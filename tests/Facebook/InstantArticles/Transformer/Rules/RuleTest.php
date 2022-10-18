<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    public function testCreateFromPropertiesThrowsException()
    {
        $this->expectException( \Exception::class );
        $this->expectExceptionMessage( 'All Rule class extensions should implement the Rule::createFrom($configuration) method' );

        Rule::createFrom([]);
    }

    public function testCreateThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('All Rule class extensions should implement the Rule::create() method');

        Rule::create();
    }
}
