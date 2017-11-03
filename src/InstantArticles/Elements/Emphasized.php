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
 * An emphasized text.
 *
 * @see {link:https://developers.facebook.com/docs/instant-articles/reference/body-text}
 */
class Emphasized extends FormattedText
{
    private function __construct()
    {
    }

    /**
     * @return Emphasized
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Structure and create <em> node.
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

        $emphasized = $document->createElement('em');

        $emphasized->appendChild($this->textToDOMDocumentFragment($document));

        return $emphasized;
    }
}
