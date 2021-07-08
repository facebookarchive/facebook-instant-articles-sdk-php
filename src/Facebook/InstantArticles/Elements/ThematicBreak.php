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
 * A  thematic break aka horizontal rule.
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
class ThematicBreak extends FormattedText
{
    private function __construct()
    {
    }

    /**
     * @return  ThematicBreak
     */
    public static function create()
    {
        return new self();
    }

    public function appendText($text)
    {
        throw new \BadMethodCallException('Cannot append text to a thematic break');
    }

    /**
     * Structure and create <hr> node.
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

        $hr = $document->createElement('hr');

        return $hr;
    }

    /**
     * Overrides the TextContainer::isValid() to a always valid one, since
     * <hr> tag will never be "invalid".
     * @see TextContainer::isValid().
     */
    public function isValid()
    {
        return true;
    }
}
