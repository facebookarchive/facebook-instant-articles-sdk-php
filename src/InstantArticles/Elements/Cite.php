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
 * Title for the Document
 *
 * Example:
 * <cite> This is the first Instant Article</cite>
 *  or
 * <cite> This is the <b>first</b> Instant Article</cite>
 */
class Cite extends TextContainer
{
    /**
     * @var string text align. Values: "op-left"|"op-center"|"op-right"
     */
    private $textAlignment;

    /**
     * @var string vertical align. Values: "op-vertical-top"|"op-vertical-bottom"|"op-vertical-center"
     */
    private $verticalAlignment;

    /**
     * @var string text position. Values: "op-vertical-below"|"op-vertical-above"|"op-vertical-center"
     */
    private $position;

    private function __construct()
    {
    }

    /**
     * @return Cite
     */
    public static function create()
    {
        return new self();
    }

    /**
     * The Text alignment that will be used.
     *
     * @see Caption::ALIGN_RIGHT
     * @see Caption::ALIGN_LEFT
     * @see Caption::ALIGN_CENTER
     *
     * @param string $text_alignment alignment option that will be used.
     *
     * @return $this
     */
    public function withTextAlignment($text_alignment)
    {
        Type::enforceWithin(
            $text_alignment,
            [
                Caption::ALIGN_RIGHT,
                Caption::ALIGN_LEFT,
                Caption::ALIGN_CENTER
            ]
        );
        $this->textAlignment = $text_alignment;

        return $this;
    }

    /**
     * The vertical alignment that will be used.
     *
     * @see Caption::VERTICAL_TOP
     * @see Caption::VERTICAL_BOTTOM
     * @see Caption::VERTICAL_CENTER
     *
     * @param string $vertical_alignment alignment option that will be used.
     *
     * @return $this
     */
    public function withVerticalAlignment($vertical_alignment)
    {
        Type::enforceWithin(
            $vertical_alignment,
            [
                Caption::VERTICAL_TOP,
                Caption::VERTICAL_BOTTOM,
                Caption::VERTICAL_CENTER
            ]
        );
        $this->verticalAlignment = $vertical_alignment;

        return $this;
    }

    /**
     * @deprecated
     *
     * @param string $position
     * @return $this
     */
    public function withPostion($position)
    {
        return $this->withPosition($position);
    }

    /**
     * The Text position that will be used.
     *
     * @see Caption::POSITION_ABOVE
     * @see Caption::POSITION_BELOW
     * @see Caption::POSITION_CENTER
     *
     * @param string $position that will be used.
     * @return $this
     */
    public function withPosition($position)
    {
        Type::enforceWithin(
            $position,
            [
                Caption::POSITION_ABOVE,
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER
            ]
        );
        $this->position = $position;

        return $this;
    }

    /**
     * Structure and create the <cite> in a DOMElement.
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

        $cite = $document->createElement('cite');

        $classes = [];
        if ($this->position) {
            $classes[] = $this->position;
        }
        if ($this->textAlignment) {
            $classes[] = $this->textAlignment;
        }
        if ($this->verticalAlignment) {
            $classes[] = $this->verticalAlignment;
        }
        if (!empty($classes)) {
            $cite->setAttribute('class', implode(' ', $classes));
        }
        $cite->appendChild($this->textToDOMDocumentFragment($document));

        return $cite;
    }
}
