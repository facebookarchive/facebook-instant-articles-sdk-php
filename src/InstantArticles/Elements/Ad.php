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
 * An Ad within for the article.
 *
 * Example:
 * <figure class="op-ad">
 *     <iframe height="50" width="320">
 *        <!-- Include full ad code here -->
 *     </iframe>
 * </figure>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/ad}
 */
class Ad extends ElementWithHTML
{
    /**
     * @var int The height of your ad.
     */
    private $height;

    /**
     * @var string The source of the content for your ad.
     */
    private $source;

    /**
     * @var int The width of your ad.
     */
    private $width;

    private function __construct()
    {
    }

    public static function create()
    {
        return new self();
    }

    /**
     * @var boolean Ad will be reused if additional placement slots are available. False by default.
     */
    private $isDefaultForReuse = false;

    /**
     * Ad will be reused in additional impression slots.
     */
    public function enableDefaultForReuse()
    {
        $this->isDefaultForReuse = true;
        return $this;
    }

    /**
     * Ad will not be used in additional impression slots.
     */
    public function disableDefaultForReuse()
    {
        $this->isDefaultForReuse = false;
        return $this;
    }

    /**
     * Sets the height of your ad.
     *
     * @param int $height The height of your ad.
     *
     * @return $this
     */
    public function withHeight($height)
    {
        Type::enforce($height, Type::INTEGER);
        $this->height = $height;

        return $this;
    }

    /**
     * Sets the source for the ad.
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
     * Sets the width of your ad.
     *
     * @param int $width The width of your ad.
     *
     * @return $this
     */
    public function withWidth($width)
    {
        Type::enforce($width, Type::INTEGER);
        $this->width = $width;

        return $this;
    }

    /**
     * @return bool True if Ad has been set to reusable.
     */
    public function getIsDefaultForReuse()
    {
        return $this->isDefaultForReuse;
    }

    /**
     * Gets the height of your ad.
     *
     * @return int The height of your ad.
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Gets the source for the ad.
     *
     * @return string The source of the content for your ad.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Gets the width of your ad.
     *
     * @return int The width of your ad.
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Structure and create the full Ad in a DOMElement.
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
        $figure->setAttribute(
            'class',
            'op-ad'
            .($this->isDefaultForReuse ? ' op-ad-default' : '')
        );

        if ($this->source) {
            $iframe->setAttribute('src', $this->source);
        }

        if ($this->width) {
            $iframe->setAttribute('width', $this->width);
        }

        if ($this->height) {
            $iframe->setAttribute('height', $this->height);
        }

        // Ad markup
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
     * @return true for valid Ad that contains valid src or html, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->source) || $this->html;
    }
}
