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
 * Base class for components accepting formatted text. It can contain bold, italic and links.
 *
 * Example:
 * This is a <b>formatted</b> <i>text</i> for <a href="https://foo.com">your article</a>.
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
abstract class TextContainer extends Element implements ChildrenContainer
{
    /**
     * @var array The content is a list of strings and FormattingElements
     */
    private $textChildren = array();

    /**
     * Adds content to the formatted text.
     *
     * @param string|FormattedText|TextContainer The content can be a string or a FormattedText.
     *
     * @return $this
     */
    public function appendText($child)
    {
        Type::enforce($child, [Type::STRING, FormattedText::getClassName(), TextContainer::getClassName()]);
        $this->textChildren[] = $child;

        return $this;
    }

    /**
     * Clears the text.
     */
    public function clearText()
    {
        $this->textChildren = array();
    }

    /**
     * @return string[]|FormattedText[]|TextContainer[] All text token for this text container.
     */
    public function getTextChildren()
    {
        return $this->textChildren;
    }

    /**
     * Structure and create the full text in a DOMDocumentFragment.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
     *
     * @return \DOMDocumentFragment
     */
    public function textToDOMDocumentFragment($document = null)
    {
        if (!$document) {
            $document = new \DOMDocument();
        }

        $fragment = $document->createDocumentFragment();

        // Generate markup
        foreach ($this->textChildren as $content) {
            if (Type::is($content, Type::STRING)) {
                $text = $document->createTextNode($content);
                $fragment->appendChild($text);
            } else {
                $fragment->appendChild($content->toDOMElement($document));
            }
        }

        if (!$fragment->hasChildNodes()) {
            $fragment->appendChild($document->createTextNode(''));
        }

        return $fragment;
    }

    /**
     * Build up a string with the content from children text container
     *
     * @return string the unformated plain text content from children
     */
    public function getPlainText()
    {
        $text = '';

        // Generate markup
        foreach ($this->textChildren as $content) {
            if (Type::is($content, Type::STRING)) {
                $text .= $content;
            } else {
                $text .= $content->getPlainText();
            }
        }

        return $text;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid tag, false otherwise.
     */
    public function isValid()
    {
        $textContent = '';

        foreach ($this->textChildren as $content) {
            // Recursive check on TextContainer, if something inside is valid, this is valid.
            if (Type::is($content, TextContainer::getClassName()) && $content->isValid()) {
                return true;
            // If is string content, concat to check if it is not only a bunch of empty chars.
            } elseif (Type::is($content, Type::STRING)) {
                $textContent = $textContent.$content;
            }
        }

        return !Type::isTextEmpty($textContent);
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return array of TextContainer
     */
    public function getContainerChildren()
    {
        $children = array();

        foreach ($this->textChildren as $content) {
            // Recursive check on TextContainer, if something inside is valid, this is valid.
            if (Type::is($content, TextContainer::getClassName())) {
                $children[] = $content;
            }
        }

        return $children;
    }
}
