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
 * Each paragraph of article should be an instance of this class.
 *
 * Example:
 * <aside> This is the pullquote </p>
 *
 * or
 *
 * <aside>
 *    Long life, pull quote will have.
 *    <cite>Unknown Jedi</cite>
 * </aside>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
class Pullquote extends TextContainer
{
    /**
     * @var Cite Content that will be shown on <cite>...</cite> tags.
     */
    private $attribution;

    private function __construct()
    {
    }

    /**
     * @return Pullquote
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the attribution string
     *
     * @param string|Cite $attribution The attribution text
     *
     * @return $this
     */
    public function withAttribution($attribution)
    {
        Type::enforce($attribution, [Type::STRING, Cite::getClassName()]);
        if (Type::is($attribution, Type::STRING)) {
            $this->attribution = Cite::create()->appendText($attribution);
        } else {
            $this->attribution = $attribution;
        }

        return $this;
    }

    /**
     * @return Cite The attribution
     */
    public function getAttribution()
    {
        return $this->attribution;
    }

    /**
     * Structure and create the full Pullquote in a DOMElement.
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

        $element = $document->createElement('aside');

        $element->appendChild($this->textToDOMDocumentFragment($document));

        // Attribution Citation
        if ($this->attribution) {
            $element->appendChild($this->attribution->toDOMElement($document));
        }

        return $element;
    }
}
