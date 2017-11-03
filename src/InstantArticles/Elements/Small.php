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
 * A small Text container used in footer
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/footer}
 */
class Small extends TextContainer
{
    private function __construct()
    {
    }

    /**
     * @return Small
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Structure and create <small> node.
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

        $small = $document->createElement('small');

        $small->appendChild($this->textToDOMDocumentFragment($document));

        return $small;
    }
}
