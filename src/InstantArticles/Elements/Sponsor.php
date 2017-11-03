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
 * Class Sponsor that represents branded content.
 *
 * Example:
 * <ul class="op-sponsors">
 *     <li><a href="http://facebook.com/your-sponsor" rel="facebook"></a></li>
 * </ul>
 *
 */
class Sponsor extends Element
{
    /**
     * @var string page URL.
     */
    private $page_url;

    /**
     * Factory method for a Sponsor.
     *
     * @return Sponsor the new instance.
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the page url sponsor. Overrides the previous set URL.
     *
     * @param string The page url that will be the sponsor.
     *
     * @return $this
     */
    public function withPageUrl($url)
    {
        Type::enforce($url, Type::STRING);
        $this->page_url = $url;
        return $this;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid ListElement that contains at least one ListItem's valid, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->page_url);
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

        $element = $document->createElement('ul');
        $element->setAttribute('class', 'op-sponsors');

        $item = $document->createElement('li');
        $element->appendChild($item);

        $anchor = $document->createElement('a');
        $item->appendChild($anchor);

        $anchor->setAttribute('href', $this->page_url);
        $anchor->setAttribute('rel', 'facebook');
        $anchor->appendChild($document->createTextNode(''));

        return $element;
    }
}
