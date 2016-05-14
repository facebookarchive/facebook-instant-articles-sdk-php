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
 * Each paragraph of article should be an instance of this class.
 *
 * Example:
 * <p> This is the first paragraph of body text. </p>
 *
 * or
 *
 * <p> This is the <i>second</i> paragraph of <b>body text</b>. </p>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
class Paragraph extends TextContainer
{
    private function __construct()
    {
    }

    /**
     * @return Paragraph
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Structure and create the full Paragraph in a DOMElement.
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

        $paragraph = $document->createElement('p');

        $paragraph->appendChild($this->textToDOMDocumentFragment($document));

        return $paragraph;
    }
}
