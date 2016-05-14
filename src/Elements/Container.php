<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
 namespace Facebook\InstantArticles\Elements;

 /**
  * Interface Container
  * This interface specifies the navigatable objects that have children.
  */
interface Container
{

    /**
     * Must return an array of Element typed objects.
     * To navigate thru the Container object tree, always check if it is a Container.
     * <code>
     *     if (Type::is($object, Container::getClassName())) {
     *         foreach($object->getChildren() as $child) {
     *              //$child operations
     *         }
     *     }
     * </code>
     *
     * @return array(<Element>) All implementing classes returns an array of Elements.
     */
    public function getContainerChildren();

    /**
     * Auxiliary method to extract all Elements full qualified class name.
     *
     * @return string The full qualified name of class
     */
    public static function getClassName();
}
