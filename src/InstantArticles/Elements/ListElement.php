<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Validators\Type;

/**
 * Class List that represents a simple HTML list
 *
 * Example Unordered:
 * <ul>
 *     <li>Dog</li>
 *     <li>Cat</li>
 *     <li>Fox</li>
 * </ul>
 *
 * Example Ordered:
 * <ol>
 *     <li>Groceries</li>
 *     <li>School</li>
 *     <li>Sleep</li>
 * </ol>
 */
class ListElement extends Element implements ChildrenContainer
{
    /**
     * @var boolean Checks if it is ordered or unordered
     */
    private $isOrdered = false;

    /**
     * @var ListItem[] Items of the list
     */
    private $items = [];

    protected function __construct()
    {
    }

    /**
     * Factory method for an Ordered list
     *
     * @return ListElement the new instance List as an ordered list
     */
    public static function createOrdered()
    {
        $list = new self();
        $list->enableOrdered();

        return $list;
    }

    /**
     * Factory method for an unordered list
     *
     * @return ListElement the new instance List as an unordered list
     */
    public static function createUnordered()
    {
        $list = new self();
        $list->disableOrdered();

        return $list;
    }

    /**
     * Adds a new item to the List
     *
     * @param string|ListItem $new_item The new item that will be pushed to the end of the list
     *
     * @return $this
     */
    public function addItem($new_item)
    {
        Type::enforce($new_item, [ListItem::getClassName(), Type::STRING]);
        if (Type::is($new_item, Type::STRING)) {
            $new_item = ListItem::create()->appendText($new_item);
        }
        $this->items[] = $new_item;

        return $this;
    }

    /**
     * Sets all items of the list as the array on the parameter
     *
     * @param string|[]ListItem[] $new_items The new items. Replaces all items from the list
     *
     * @return $this
     */
    public function withItems($new_items)
    {
        Type::enforceArrayOf($new_items, [ListItem::getClassName(), Type::STRING]);
        $this->items = [];
        foreach ($new_items as $new_item) {
            $this->addItem($new_item);
        }

        return $this;
    }

    /**
     * Makes the list become ordered
     *
     * @return $this
     */
    public function enableOrdered()
    {
        $this->isOrdered = true;

        return $this;
    }

    /**
     * Makes the list become unordered
     *
     * @return $this
     */
    public function disableOrdered()
    {
        $this->isOrdered = false;

        return $this;
    }

    /**
     * @return string[]|ListItem[] the list text items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return boolean if the list is ordered
     */
    public function isOrdered()
    {
        return $this->isOrdered;
    }

    /**
     * Structure and create the full Video in a XML format DOMElement.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
     *
     * @return \DOMElement
     */
    public function toDOMElement($document = null)
    {
        if (!$document) {
            $document = new \DOMDocument();
        }

        if (!$this->isValid()) {
            return $this->emptyElement($document);
        }

        if ($this->isOrdered) {
            $element = $document->createElement('ol');
        } else {
            $element = $document->createElement('ul');
        }

        if ($this->items) {
            foreach ($this->items as $item) {
                if ($item) {
                    $element->appendChild($item->toDOMElement($document));
                }
            }
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid ListElement that contains at least one ListItem's valid, false otherwise.
     */
    public function isValid()
    {
        foreach ($this->items as $item) {
            if ($item->isValid()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return array of Elements contained by Image.
     */
    public function getContainerChildren()
    {
        $children = array();
        if ($this->items) {
            foreach ($this->items as $item) {
                $children[] = $item;
            }
        }
        return $children;
    }
}
