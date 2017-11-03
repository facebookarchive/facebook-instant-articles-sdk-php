<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Validators;

/**
 * Class that have all the typechecks and sizechecks for elements and classes
 * that needs to be well checked.
 *
 * is*() prefixed methods return boolean
 *
 * enforce*() prefixed methods return true for success and throw
 * InvalidArgumentException for the invalid cases.
 */
class Type
{
    const STRING = 'STRING';
    const INTEGER = 'INTEGER';
    const BOOLEAN = 'BOOLEAN';
    const FLOAT = 'FLOAT';
    const ARRAY_TYPE = 'ARRAY_TYPE';

    /**
     * This method enforces the $var param to be instanceof one of the $types_allowed informed.
     * It will throw exception if not satisfied
     *
     * @param mixed $var The object that will be verified
     * @param mixed $types_allowed array of classes or one single class
     * @return boolean true when success, or throws exception when not satisfied
     * @throws \InvalidArgumentException when $var doesn't comply with $types_allowed
     */
    public static function enforce($var, $types_allowed)
    {
        return self::is($var, $types_allowed, true);
    }

    /**
     * This method checks if the $var param is instanceof one of the $types_allowed informed.
     * It will return the success of the check.
     *
     * @param mixed $var The object that will be verified.
     * @param mixed $types_allowed array of classes or one single class.
     * @param boolean $enforce If informed with true, it works as (Type::enforce()) method.
     * @return boolean true when success, false when failed the check.
     * @throws \InvalidArgumentException if $enforced is true and $var doesn't comply with the $types_allowed.
     * @see Type::enforce().
     */
    public static function is($var, $types_allowed, $enforce = false)
    {
        if (is_array($types_allowed)) {
            foreach ($types_allowed as $type) {
                if (($var instanceof $type) || self::isPrimitive($var, $type)) {
                    return true;
                }
            }
            if ($enforce) {
                self::throwException($var, $types_allowed);
            }
            return false;
        } else {
            $result = ($var instanceof $types_allowed) ||
                self::isPrimitive($var, $types_allowed);
            if (!$result && $enforce) {
                self::throwException($var, $types_allowed);
            }
            return $result;
        }
    }


    /**
     * Auxiliary function to check for the primitive types.
     *
     * @param mixed $var the Variable that will be testRenderBasic
     * @param string $type one of the Type::const that will be the set of possible values for $var
     *
     * @see Type::STRING
     * @see Type::INTEGER
     * @see Type::FLOAT
     * @see Type::BOOLEAN
     * @see Type::ARRAY_TYPE
     *
     * @return bool
     */
    private static function isPrimitive($var, $type)
    {
        switch ($type) {
            case Type::STRING:
                return is_string($var);
            case Type::INTEGER:
                return is_int($var);
            case Type::FLOAT:
                return is_float($var);
            case Type::BOOLEAN:
                return is_bool($var);
            case Type::ARRAY_TYPE:
                return is_array($var);
        }
        return false;
    }

    /**
     * Method that checks if all elements in one array comply with the types
     * inside the $types_allowed. If any element on that array doesnt meet the
     * expectations an InvalidArgumentException will be thrown.
     *
     * @param array $var the target variable to be checked. REQUIRED must be array
     * @param string|string[] $types_allowed The set of classes that $var will be checked against
     *
     * @return bool
     *
     * @throws \InvalidArgumentException when not all items in an array are from the types in $types_allowed
     *
     * @see Type::STRING
     * @see Type::INTEGER
     * @see Type::FLOAT
     * @see Type::BOOLEAN
     */
    public static function enforceArrayOf(
        $var,
        $types_allowed
    ) {
        return self::isArrayOf($var, $types_allowed, true);
    }

    /**
     * Method that checks if all elements in one array comply with the types
     * inside the $types_allowed. If any element on that array doesn't meet the
     * expectations false will be returned
     *
     * @param array $var the target variable to be checked. REQUIRED must be array
     * @param array|string $types_allowed The set of classes that $var will be checked against
     * @return true for success, false otherwise
     *
     * @see Type::STRING
     * @see Type::INTEGER
     * @see Type::FLOAT
     * @see Type::BOOLEAN
     */
    public static function isArrayOf(
        $var,
        $types_allowed,
        $enforce = false
    ) {
        if (!is_array($var)) {
            if ($enforce) {
                throw new \InvalidArgumentException(
                    'Type::isArrayOf() expects'.
                    'first parameter to be an array.'
                );
            }
            return false;
        }
        foreach ($var as $item) {
            if (!self::is($item, $types_allowed, $enforce)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Auxiliary method that formats the message string and throws the Exception
     */
    private static function throwException($var, $types_allowed)
    {
        // stringify the $var parameter
        ob_start();
        var_dump($var);
        $var_str = ob_get_clean();

        // stringify the $types_allowed parameter
        ob_start();
        var_dump($types_allowed);
        $types_str = ob_get_clean();

        throw new \InvalidArgumentException(
            "Method expects this value \n----[\n".$var_str."]----\n".
            " to be one of the types \n====[\n".$types_str."]===="
        );
    }

    /**
     * Method that enforces the array size to be EXACTLY the $size informed. If
     * the size differs from the $size it will throw InvalidArgumentException
     *
     * @param array $array the array that will be checked
     * @param int $size The EXACTLY size that array must have
     *
     * @return bool
     */
    public static function enforceArraySize($array, $size)
    {
        return self::isArraySize($array, $size, true);
    }

    /**
     * Method that checks the array size to be EXACTLY the $size informed. If
     * the size differs from the $size it will return false, otherwise true.
     * @param array $array the array that will be checked
     * @param int $size The EXACTLY size that array must have
     * @return true if matches the size, false otherwise
     */
    public static function isArraySize($array, $size, $enforce = false)
    {
        if (!is_array($array)) {
            if ($enforce) {
                throw new \InvalidArgumentException(
                    'Type::isArraySize() expects'.
                    'first parameter to be an array.'
                );
            }
            return false;
        }

        $meets_size = count($array) == $size;
        if ($enforce && !$meets_size) {
            self::throwArrayException($array, $size, 'Exact size');
        }
        return $meets_size;
    }

    /**
     * Method that enforces the array to have at least $min_size of elements. If
     * the size is less than $min_size it will throw InvalidArgumentException
     * I.e.: array (1,2,3), $min_size 3 = true
     * I.e.: array (1,2,3), $min_size 4 = throws InvalidArgumentException
     *
     * @param array $array the array that will be checked
     * @param int $min_size The EXACTLY size that array must have
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if $array doesn't have at least $min_size items
     */
    public static function enforceArraySizeGreaterThan($array, $min_size)
    {
        return self::isArraySizeGreaterThan($array, $min_size, true);
    }

    /**
     * Method that checks if the array has at least $min_size of elements. If
     * the size is less than $min_size it will return false.
     * I.e.: array (1,2,3), $min_size 3 = true
     * I.e.: array (1,2,3), $min_size 4 = false
     *
     * @param array $array the array that will be checked
     * @param int $min_size The minimum elements the array must have
     *
     * @return bool true if has at least $min_size, false otherwise
     */
    public static function isArraySizeGreaterThan($array, $min_size, $enforce = false)
    {
        if (!is_array($array)) {
            if ($enforce) {
                throw new \InvalidArgumentException(
                    'Type::isArraySizeGreaterThan() expects'.
                    'first parameter to be an array.'
                );
            }
            return false;
        }

        $meets_size = count($array) >= $min_size;
        if ($enforce && !$meets_size) {
            self::throwArrayException($array, $min_size, 'Minimal size');
        }
        return $meets_size;
    }

    /**
     * Method that enforces the array to have at most $max_size of elements. If
     * the size is more than $max_size it will throw InvalidArgumentException
     * I.e.: array (1,2,3), $max_size 3 = true
     * I.e.: array (1,2,3), $max_size 2 = throws InvalidArgumentException
     *
     * @param array $array the array that will be checked
     * @param int $max_size The maximum number of items the array can have
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if $array have more than $max_size items
     */
    public static function enforceArraySizeLowerThan($array, $max_size)
    {
        return self::isArraySizeLowerThan($array, $max_size, true);
    }

    /**
     * Method that checks if the array has at most $max_size of elements. If
     * the size is more than $max_size it will return false
     * I.e.: array (1,2,3), $max_size 3 = true
     * I.e.: array (1,2,3), $max_size 2 = false
     *
     * @param array $array the array that will be checked
     * @param int $max_size The maximum number of items the array can have
     * @param boolean $enforce works as Type::enforceArrayMaxSize().
     * @see Type::enforceArrayMaxSize().
     *
     * @return bool true if it has less elements than $max_size, false otherwise
     */
    public static function isArraySizeLowerThan($array, $max_size, $enforce = false)
    {
        if (!is_array($array)) {
            if ($enforce) {
                throw new \InvalidArgumentException(
                    'Type::isArraySizeLowerThan() expects'.
                    'first parameter to be an array.'
                );
            }
            return false;
        }

        $meets_size = count($array) <= $max_size;
        if ($enforce && !$meets_size) {
            self::throwArrayException($array, $max_size, 'Maximum size');
        }
        return $meets_size;
    }

    /*
     * Utility method that constructs the message about array sizes an throws.
     */
    private static function throwArrayException($array, $size, $message)
    {
        $error_message =
            'Array expects a '.$message.' of '.$size.
            ' but received an array with '.count($array).' items.';

        throw new \InvalidArgumentException($error_message);
    }

    /**
     * Method that checks if the value is in the possible ones from the set to
     * compare against
     *
     * @param mixed $value The value that will be verified
     * @param array $universe The universe the $value must be in.
     * @return true if the value is IN the universe, false otherwise.
     */
    public static function isWithin($value, $universe, $enforce = false)
    {
        $within = in_array($value, $universe, true);
        if (!$within && $enforce) {
            self::throwNotWithinException($value, $universe);
        }

        return $within;
    }

    /**
     * Method that enforces the value to be IN the universe informed, if not an
     * exception will be thrown.
     *
     * @param mixed $value The value that will be verified
     * @param array $universe The universe the $value must be in.
     * @return true if the value is IN the universe, throws Exception otherwise.
     * @throws \InvalidArgumentException if the value not IN the expected universe.
     */
    public static function enforceWithin($value, $universe)
    {
        return self::isWithin($value, $universe, true);
    }

    private static function throwNotWithinException($value, $universe)
    {
        $value_str = self::stringify($value);
        $universe_str = self::stringify($universe);

        throw new \InvalidArgumentException(
            "Method expects this value \n----[\n".$value_str."]----\n".
            " to be within this universe of values \n====[\n".$universe_str."]===="
        );
    }

    /**
     * Checks the thext if it is empty.
     * Examples:
     * "" => true
     * "    " => true
     * "\n" => true
     * "a" => false
     * "  a  " => false
     * "&nbsp;" => true
     *
     * @param string $text The text that will be checked.
     * @return true if empty, false otherwise.
     */
    public static function isTextEmpty($text)
    {
        if (!isset($text) || $text === null || !self::is($text, self::STRING)) {
            return true;
        }
        // Stripes empty spaces, &nbsp;, <br/>, new lines
        $text = preg_replace("/\s+/", "", $text);
        $text = str_replace("&nbsp;", "", $text);

        return empty($text);
    }

    /**
     * Auxiliary method that stringify an object as var_dump does.
     * @return string $object var_dump result.
     */
    public static function stringify($object)
    {
          // stringify the $object parameter
          return var_export($object, true);
    }

    /**
     * Checks if tag is a DOMElement and also to have the $tagName informed.
     * @param $tag DOMElement the tag element that will be validated
     * @param $tagName string Tag name to be validated
     * @return boolean true if it is Element and from same tagName.
     * @throws InvalidArgumentException in case $tag is not DOMElement.
     */
    public static function isElementTag($tag, $tagName)
    {
        $element = new \DOMElement('dummy'); // This is just for retro-compatibility for 5.4 ::class placeholder
        self::enforce($tag, get_class($element));
        return $tag->tagName === $tagName;
    }

    /**
     * Enforces tag to be an DOMElement and also to have the $tagName informed.
     * @param $tag DOMElement the tag element that will be validated
     * @param $tagName string Tag name to be validated
     * @throws InvalidArgumentException in case $tag is not DOMElement or same tagName.
     */
    public static function enforceElementTag($tag, $tagName)
    {
        if (!self::isElementTag($tag, $tagName)) {
            throw new \InvalidArgumentException(
                "Tag <".$tagName."> expected, <".$tag->tagName."> informed."
            );
        }
    }
}
