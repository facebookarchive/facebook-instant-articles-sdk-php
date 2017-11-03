<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

class GetterFactory
{
    const TYPE_STRING_GETTER = 'string';
    const TYPE_INTEGER_GETTER = 'int';
    const TYPE_DATE_GETTER = 'date';
    const TYPE_CHILDREN_GETTER = 'children';
    const TYPE_ELEMENT_GETTER = 'element';
    const TYPE_CONSTANT_GETTER = 'constant';
    const TYPE_FRAGMENT_GETTER = 'fragment';
    const TYPE_NEXTSIBLING_GETTER = 'sibling';
    const TYPE_NEXTSIBLINGELEMENT_GETTER = 'next-sibling-element-of';
    const TYPE_EXISTS_GETTER = 'exists';
    const TYPE_JSON_GETTER = 'json';
    const TYPE_XPATH_GETTER = 'xpath';
    const TYPE_MULTIPLEELEMENTS_GETTER = 'multiple';

    /**
     * Creates an Getter class.
     *
     *  array(
     *        type => 'string' | 'children',
     *        selector => 'img.cover',
     *        [attribute] => 'src'
     *    )
     * @see StringGetter
     * @see ChildrenGetter
     * @see IntegerGetter
     * @see DateGetter
     * @see ElementGetter
     * @see ConstantGetter
     * @see NextSiblingGetter
     * @see ExistsGetter
     * @see JSONGetter
     * @see XpathGetter
     *
     * @param string[] $getter_configuration that maps the properties for getter
     *
     * @return AbstractGetter
     */
    public static function create($getter_configuration)
    {
        $GETTERS = [
            self::TYPE_STRING_GETTER => StringGetter::getClassName(),
            self::TYPE_INTEGER_GETTER => IntegerGetter::getClassName(),
            self::TYPE_DATE_GETTER => DateGetter::getClassName(),
            self::TYPE_CHILDREN_GETTER => ChildrenGetter::getClassName(),
            self::TYPE_ELEMENT_GETTER => ElementGetter::getClassName(),
            self::TYPE_FRAGMENT_GETTER => FragmentGetter::getClassName(),
            self::TYPE_CONSTANT_GETTER => ConstantGetter::getClassName(),
            self::TYPE_NEXTSIBLING_GETTER => NextSiblingGetter::getClassName(),
            self::TYPE_NEXTSIBLINGELEMENT_GETTER => NextSiblingElementGetter::getClassName(),
            self::TYPE_EXISTS_GETTER => ExistsGetter::getClassName(),
            self::TYPE_JSON_GETTER => JSONGetter::getClassName(),
            self::TYPE_XPATH_GETTER => XpathGetter::getClassName(),
            self::TYPE_MULTIPLEELEMENTS_GETTER => MultipleElementsGetter::getClassName()
        ];

        $class = $getter_configuration['type'];
        if (array_key_exists($class, $GETTERS)) {
            $class = $GETTERS[$class];
        }
        $instance = new $class();
        $instance->createFrom($getter_configuration);
        return $instance;
    }
}
