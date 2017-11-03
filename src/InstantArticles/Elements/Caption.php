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
 * A caption for any element.
 * A caption can be included in any of the items:
 * <ul>
 *     <li>Image</li>
 *     <li>Video</li>
 *     <li>SlideShow</li>
 *     <li>Map</li>
 *     <li>Interactive</li>
 * </ul>.
 *
 * Example:
 *    <figcaption class="op-vertical-below">
 *        <h1>Caption Title</h1>
 *        <h2>Caption SubTitle</h2>
 *    </figcaption>
 *
 * @see Image
 * @see Video
 * @see SlideShow
 * @see Map
 * @see Interactive
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/caption}
 */
class Caption extends FormattedText
{
    // Font size
    const SIZE_SMALL = 'op-small';
    const SIZE_MEDIUM = 'op-medium';
    const SIZE_LARGE = 'op-large';
    const SIZE_XLARGE = 'op-extra-large';

    // Text alignment (horizontal)
    const ALIGN_LEFT = 'op-left';
    const ALIGN_CENTER = 'op-center';
    const ALIGN_RIGHT = 'op-right';

    // Vertical position of the block
    const POSITION_BELOW = 'op-vertical-below';
    const POSITION_ABOVE = 'op-vertical-above';
    const POSITION_CENTER = 'op-vertical-center';

    // Vertical alignment of the block
    const VERTICAL_TOP = 'op-vertical-top';
    const VERTICAL_BOTTOM = 'op-vertical-bottom';
    const VERTICAL_CENTER = 'op-vertical-center';

    /**
     * @var H1 The caption title. REQUIRED
     */
    private $title;

    /**
     * @var H2 The caption subtitle. optional
     */
    private $subTitle;

    /**
     * @var Cite The credit text. optional
     */
    private $credit;

    /**
     * @var string text Size. Values: "op-small"|"op-medium"|"op-large"|"op-extra-large"
     */
    private $fontSize;

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
     * @return Caption
     */
    public static function create()
    {
        return new self();
    }

    /**
     * The caption title. REQUIRED.
     *
     * @param H1|string $title the caption text that will be shown
     *
     * @return $this
     */
    public function withTitle($title)
    {
        Type::enforce($title, [Type::STRING, H1::getClassName()]);

        if (Type::is($title, Type::STRING)) {
            $title = H1::create()->appendText($title);
        }
        $this->title = $title;

        return $this;
    }

    /**
     * The caption sub title. optional.
     *
     * @param string $sub_title the caption sub title text that will be shown
     *
     * @return $this
     */
    public function withSubTitle($sub_title)
    {
        Type::enforce($sub_title, [Type::STRING, H2::getClassName()]);
        if (Type::is($sub_title, Type::STRING)) {
            $sub_title = H2::create()->appendText($sub_title);
        }
        $this->subTitle = $sub_title;

        return $this;
    }

    /**
     * The caption credit. optional.
     *
     * @param string $credit the caption credit text that will be shown
     *
     * @return $this
     */
    public function withCredit($credit)
    {
        Type::enforce($credit, [Type::STRING, Cite::getClassName()]);
        if (Type::is($credit, Type::STRING)) {
            $credit = Cite::create()->appendText($credit);
        }
        $this->credit = $credit;

        return $this;
    }

    /**
     * The Fontsize that will be used.
     *
     * @see Caption::SIZE_SMALL
     * @see Caption::SIZE_MEDIUM
     * @see Caption::SIZE_LARGE
     * @see Caption::SIZE_XLARGE
     *
     * @param string $font_size the caption font size that will be used.
     *
     * @return $this
     */
    public function withFontsize($font_size)
    {
        Type::enforceWithin(
            $font_size,
            [
                Caption::SIZE_XLARGE,
                Caption::SIZE_LARGE,
                Caption::SIZE_MEDIUM,
                Caption::SIZE_SMALL
            ]
        );
        $this->fontSize = $font_size;

        return $this;
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
     *
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
     *
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
     * @return string the caption text title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string the caption text subtitle
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @return string the credit text
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @return string the Font size.
     *
     * @see Caption::SIZE_SMALL
     * @see Caption::SIZE_MEDIUM
     * @see Caption::SIZE_LARGE
     * @see Caption::SIZE_XLARGE
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @return string the Font size.
     *
     * @see Caption::ALIGN_RIGHT
     * @see Caption::ALIGN_LEFT
     * @see Caption::ALIGN_CENTER
     */
    public function getTextAlignment()
    {
        return $this->textAlignment;
    }

    /**
     * @return string the Font size.
     *
     * @see Caption::POSITION_ABOVE
     * @see Caption::POSITION_BELOW
     * @see Caption::POSITION_CENTER
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string the Vertical Alignment.
     *
     * @see Caption::VERTICAL_TOP
     * @see Caption::VERTICAL_BOTTOM
     * @see Caption::VERTICAL_CENTER
     */
    public function getVerticalAlignment()
    {
        return $this->verticalAlignment;
    }

    /**
     * Structure and create the full ArticleImage in a XML format DOMElement.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
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

        $element = $document->createElement('figcaption');

        // title markup REQUIRED
        if ($this->title && (!$this->subTitle && !$this->credit)) {
            $element->appendChild($this->title->textToDOMDocumentFragment($document));
        } elseif ($this->title) {
            $element->appendChild($this->title->toDOMElement($document));
        }

        // subtitle markup optional
        if ($this->subTitle) {
            $element->appendChild($this->subTitle->toDOMElement($document));
        }

        $element->appendChild($this->textToDOMDocumentFragment($document));

        // credit markup optional
        if ($this->credit) {
            $element->appendChild($this->credit->toDOMElement($document));
        }

        // Formating markup
        if ($this->textAlignment || $this->verticalAlignment || $this->fontSize || $this->position) {
            $classes = [];
            if ($this->textAlignment) {
                $classes[] = $this->textAlignment;
            }
            if ($this->verticalAlignment) {
                $classes[] = $this->verticalAlignment;
            }
            if ($this->fontSize) {
                $classes[] = $this->fontSize;
            }
            if ($this->position) {
                $classes[] = $this->position;
            }
            $element->setAttribute('class', implode(' ', $classes));
        }

        return $element;
    }

    /**
     * Overrides the TextContainer::isValid().
     *
     * @see TextContainer::isValid().
     * @return true for valid Caption when it is filled, false otherwise.
     */
    public function isValid()
    {
        return
            parent::isValid() || ($this->title && $this->title->isValid());
    }
}
