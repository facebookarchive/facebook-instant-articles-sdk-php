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
 * Tracking code for your article
 *
 * Example:
 * <figure class="op-tracker">
 *     <iframe src="https://www.myserver.com/trackingcode"></iframe>
 * </figure>
 *
 * or
 *
 * <figure class="op-tracker">
 *    <iframe>
 *      <!-- Include full analytics code here -->
 *    </iframe>
 * </figure>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/analytics}
 */
class Analytics extends ElementWithHTML
{
    /**
     * @var string The source of the content for your analytics code.
     */
    private $source;

    private function __construct()
    {
    }

    public static function create()
    {
        return new self();
    }

    /**
     * Sets the source for the analytics.
     *
     * @param string $source The source of the content for your ad.
     *
     * @return $this
     */
    public function withSource($source)
    {
        Type::enforce($source, Type::STRING);
        $this->source = $source;

        return $this;
    }

    /**
     * Gets the source for the analytics.
     *
     * @return string The source of the content for your analytics.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Structure and create the full ArticleAd in a DOMElement.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
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

        $figure = $document->createElement('figure');
        $iframe = $document->createElement('iframe');

        $figure->appendChild($iframe);
        $figure->setAttribute('class', 'op-tracker');

        if ($this->source) {
            $iframe->setAttribute('src', $this->source);
        }

        // Analytics markup
        if ($this->html) {
            // Here we do not care about what is inside the iframe
            // because it'll be rendered in a sandboxed webview
            $this->dangerouslyAppendUnescapedHTML($iframe, $this->html);
        } else {
            $iframe->appendChild($document->createTextNode(''));
        }

        return $figure;
    }

    /**
    * Overrides the Element::isValid().
    * @see Element::isValid().
     * @return true for valid Analytics that contains valid source or html, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->source) || $this->html;
    }
}
