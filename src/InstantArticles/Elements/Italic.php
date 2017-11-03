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
 * An italic text.
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
class Italic extends FormattedText
{

    private function __construct()
    {
    }

    public static function create()
    {
        return new self();
    }

    /**
     * Structure and create <i> node.
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

        $italic = $document->createElement('i');

        $italic->appendChild($this->textToDOMDocumentFragment($document));

        return $italic;
    }
}
