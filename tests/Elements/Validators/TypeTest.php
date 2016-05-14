<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Validators;

use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\AnimatedGIF;

/**
 *
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{

    /*
        Type check tests ---------------
     */

    public function testIsType()
    {
        $result = Type::is(Caption::create(), [Caption::getClassName()]);
        $this->assertTrue($result);
    }

    public function testIsTypeWithArray()
    {
        $result = Type::is([1, 2, 3], Type::ARRAY_TYPE);
        $this->assertTrue($result);
    }

    public function testIsInSet()
    {
        $result = Type::is(
            Caption::create(),
            [
                Caption::getClassName(),
                InstantArticle::getClassName(),
                Video::getClassName(),
                Image::getClassName()
            ]
        );
        $this->assertTrue($result);
    }

    public function testIsNotIn()
    {
        $result = Type::is(
            Caption::create(),
            [
                Image::getClassName()
            ]
        );
        $this->assertFalse($result);
    }

    public function testIsNotInException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforce(
            Caption::create(),
            [
                Image::getClassName()
            ]
        );
    }

    public function testIsNotInEmpty()
    {
        $result = Type::is(
            Caption::create(),
            []
        );
        $this->assertFalse($result);
    }

    public function testIsNotInEmptyException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforce(
            Caption::create(),
            []
        );
    }

    public function testIsNotInSet()
    {
        $result = Type::is(
            Caption::create(),
            [
                InstantArticle::getClassName(),
                Video::getClassName(),
                Image::getClassName()
            ]
        );
        $this->assertFalse($result);
    }

    public function testIsNotInSetException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforce(
            Caption::create(),
            [
                InstantArticle::getClassName(),
                Video::getClassName(),
                Image::getClassName()
            ]
        );
    }

    public function testIsInInheritance()
    {
        $result = Type::is(
            AnimatedGIF::create(),
            [
                Image::getClassName()
            ]
        );
        $this->assertTrue($result);
    }

    public function testIsNotInInheritance()
    {
        $result = Type::is(
            AnimatedGIF::create(),
            [
                Video::getClassName()
            ]
        );
        $this->assertFalse($result);
    }

    public function testIsNotInInheritanceException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforce(
            AnimatedGIF::create(),
            [
                Video::getClassName()
            ]
        );
    }

    public function testIsString()
    {
        $result = Type::is('test', Type::STRING);
        $this->assertTrue($result);
    }

    public function testIsNotString()
    {
        $result = Type::is(1, Type::STRING);
        $this->assertFalse($result);
    }

    public function testIsNotStringException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforce(1, Type::STRING);
    }

    public function testIsArrayOfString()
    {
        $result = Type::isArrayOf(['1', '2'], Type::STRING);
        $this->assertTrue($result);
    }

    public function testIsArrayOfObject()
    {
        $result =
            Type::isArrayOf(
                [Image::create(), Image::create()],
                Image::getClassName()
            );
        $this->assertTrue($result);
    }

    public function testIsArrayOfObjects()
    {
        $result =
            Type::isArrayOf(
                [Image::create(), Video::create()],
                [Image::getClassName(), Video::getClassName()]
            );
        $this->assertTrue($result);
    }

    public function testIsArrayInInheritance()
    {
        $result = Type::isArrayOf(
            [Image::create(), AnimatedGIF::create()],
            Image::getClassName()
        );
        $this->assertTrue($result);
    }

    public function testIsNotArrayInInheritance()
    {
        $result =
            Type::isArrayOf(
                [Image::create(), Video::create()],
                Image::getClassName()
            );
        $this->assertFalse($result);
    }

    public function testIsNotArrayInInheritanceException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceArrayOf(
            [Image::create(), Video::create()],
            Image::getClassName()
        );
    }

    /*
        Array size tests ---------------
     */
    public function testArraySize()
    {
        $result = Type::isArraySize([1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testArrayNotSize()
    {
        $result = Type::isArraySize([1,2,3], 2);
        $this->assertFalse($result);
    }

    public function testArrayMinSizeExact()
    {
        $result = Type::isArraySizeGreaterThan([1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testArrayMinSizeMore()
    {
        $result = Type::isArraySizeGreaterThan([1,2,3], 2);
        $this->assertTrue($result);
    }

    public function testArrayMinSizeFew()
    {
        $result = Type::isArraySizeGreaterThan([1,2,3], 4);
        $this->assertFalse($result);
    }

    public function testEnforceArrayMinSizeException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceArraySizeGreaterThan([1,2,3], 4);
    }

    public function testArrayMaxSizeExact()
    {
        $result = Type::isArraySizeLowerThan([1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testArrayMaxSizeFew()
    {
        $result = Type::isArraySizeLowerThan([1,2,3], 4);
        $this->assertTrue($result);
    }

    public function testArrayMaxSizeMore()
    {
        $result = Type::isArraySizeLowerThan([1,2,3], 2);
        $this->assertFalse($result);
    }

    public function testEnforceArrayMaxSizeException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceArraySizeLowerThan([1,2,3], 2);
    }

    public function testIsWithinTrueString()
    {
        $result = Type::isWithin('x', ['x', 'y', 'z']);
        $this->assertTrue($result);
    }

    public function testIsWithinTrueObj()
    {
        $image = Image::create();
        $video = Video::create();
        $result = Type::isWithin($image, [$image, $video, 'z']);
        $this->assertTrue($result);
    }

    public function testIsWithinFalse()
    {
        $result = Type::isWithin('a', ['x', 'y', 'z']);
        $this->assertFalse($result);
    }

    public function testIsWithinFalseObj()
    {
        $image = Image::create();
        $video = Video::create();
        $anotherImg = Image::create();
        $result = Type::isWithin($image, [$anotherImg, $video, 'z']);
        $this->assertFalse($result);
    }

    public function testEnforceWithinTrueString()
    {
        $result = Type::enforceWithin('x', ['x', 'y', 'z']);
        $this->assertTrue($result);
    }

    public function testEnforceWithinExceptionString()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceWithin('a', ['x', 'y', 'z']);
    }

    public function testStringNotEmpty()
    {
        $this->assertFalse(Type::isTextEmpty("not empty"));
        $this->assertFalse(Type::isTextEmpty("\nnot empty\t"));
        $this->assertFalse(Type::isTextEmpty(" not empty "));
        $this->assertFalse(Type::isTextEmpty("&nbsp;not empty"));
    }

    public function testStringEmpty()
    {
        $this->assertTrue(Type::isTextEmpty(""));
        $this->assertTrue(Type::isTextEmpty("  "));
        $this->assertTrue(Type::isTextEmpty("\t\t"));
        $this->assertTrue(Type::isTextEmpty("&nbsp;"));
        $this->assertTrue(Type::isTextEmpty("\n"));
    }
}
