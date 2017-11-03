<?php

/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

namespace Facebook\InstantArticles\Transformer;

use Faker\Factory;
use Faker\Generator;

trait GeneratorTrait
{
    /**
     * @return Generator
     */
    protected function getFaker()
    {
        static $faker;

        if ($faker === null) {
            $faker = Factory::create();
            $faker->seed(9000);
        }

        return $faker;
    }
}
