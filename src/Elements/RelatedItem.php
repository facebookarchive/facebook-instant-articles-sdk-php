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
 * Class RelatedItem to represent each of the RelatedArticles.
 * @see RelatedArticles
 */
class RelatedItem extends Element
{
    /**
     * @var string The related Article URL
     */
    private $url;

    /**
     * @var boolean If the article is sponsored
     */
    private $sponsored;

    private function __construct()
    {
    }

    /**
     * Factory method for the RelatedItem
     *
     * @return RelatedItem
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the article URL
     *
     * @param string $url The related article URL
     *
     * @return $this
     */
    public function withURL($url)
    {
        Type::enforce($url, Type::STRING);
        $this->url = $url;

        return $this;
    }

    /**
     * Makes this item to be an sponsored one
     *
     * @return $this
     */
    public function enableSponsored()
    {
        $this->sponsored = true;

        return $this;
    }

    /**
     * Makes this item to *NOT* be an sponsored one
     *
     * @return $this
     */
    public function disableSponsored()
    {
        $this->sponsored = false;

        return $this;
    }

    /**
     * @return string The RelatedItem url
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * @return boolean true if it is sponsored, false otherwise.
     */
    public function isSponsored()
    {
        return $this->sponsored;
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

        $element = $document->createElement('li');
        if ($this->sponsored) {
            $element->setAttribute('data-sponsored', 'true');
        }
        $element->appendChild(
            Anchor::create()->withHref($this->url)->toDOMElement($document)
        );

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid RelatedItem that contains valid url, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->url);
    }
}
