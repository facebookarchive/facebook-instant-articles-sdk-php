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
class RelatedArticles extends Element implements Container
{
    /**
     * @var RelatedItem[] The related Articles
     */
    private $items = [];

    /**
     * @var string The title of the Related Articles content
     */
    private $title;

    private function __construct()
    {
    }

    /**
     * Factory method for the RelatedArticles list
     *
     * @return RelatedArticles the new instance of RelatedArticles
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Adds a new related article item
     *
     * @param RelatedItem $item The related article item
     *
     * @return $this
     */
    public function addRelated($item)
    {
        Type::enforce($item, RelatedItem::getClassName());
        $this->items[] = $item;

        return $this;
    }

    /**
     * Sets the title of Related articles content block
     *
     * @param string $title the name of related articles block
     *
     * @return $this
     */
    public function withTitle($title)
    {
        Type::enforce($title, Type::STRING);
        $this->title = $title;

        return $this;
    }

    /**
     * @return RelatedItem[] The RelatedItem's
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string the name of related articles block
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Structure and create the full ArticleVideo in a XML format DOMElement.
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

        $element = $document->createElement('ul');
        $element->setAttribute('class', 'op-related-articles');
        if ($this->title) {
            $element->setAttribute('title', $this->title);
        }

        if ($this->items) {
            foreach ($this->items as $item) {
                $element->appendChild($item->toDOMElement($document));
            }
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid RelatedArticles that contains at least one RelatedItem's valid, false otherwise.
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
     * Implements the Container::getContainerChildren().
     *
     * @see Container::getContainerChildren().
     * @return array of Elements contained by Image.
     */
    public function getContainerChildren()
    {
        $children = array();

        foreach ($this->items as $item) {
            $children[] = $item;
        }

        return $children;
    }
}
