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
 * A line break.
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
class LineBreak extends FormattedText
{
    private function __construct()
    {
    }

    /**
     * @return LineBreak
     */
    public static function create()
    {
        return new self();
    }

    public function appendText($text)
    {
        throw new \BadMethodCallException('Cannot append text to a line break');
    }

    /**
     * Structure and create <b> node.
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

        $br = $document->createElement('br');

        return $br;
    }

    /**
     * Overrides the TextContainer::isValid() to a always valid one, since
     * <br> tag will never be "invalid".
     * @see TextContainer::isValid().
     */
    public function isValid()
    {
        return true;
    }
}
