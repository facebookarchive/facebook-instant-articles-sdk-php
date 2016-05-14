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
 * Class Element.
 * This class is the meta each tag element that contains rendering code for the
 * tags.
 *
 */
abstract class Element
{
    abstract public function toDOMElement();

    /**
     * Renders the Element content
     *
     * @param string $doctype the doctype will be applied to document. I.e.: '<!doctype html>'.
     * @param bool $formatted Whether to format output.
     *
     * @return string with the content rendered.
     */
    public function render($doctype = '', $formatted = false)
    {
        $document = new \DOMDocument();
        $rendered = '';
        $document->preserveWhiteSpace = !$formatted;
        $document->formatOutput = $formatted;
        $element = $this->toDOMElement($document);
        $document->appendChild($element);
        $rendered = $doctype.$document->saveXML($element);

        return $rendered;
    }

    /**
     * Appends unescaped HTML to a element using the right strategy.
     *
     * @param \DOMNode $element - The element to append the HTML to.
     * @param \DOMNode $content - The unescaped HTML to append.
     */
    protected function dangerouslyAppendUnescapedHTML($element, $content)
    {
        Type::enforce($content, 'DOMNode');
        Type::enforce($element, 'DOMNode');
        $imported = $element->ownerDocument->importNode($content, true);
        $element->appendChild($imported);
    }

    /**
     * Auxiliary method to extract all Elements full qualified class name.
     *
     * @return string The full qualified name of class.
     */
    public static function getClassName()
    {
        return get_called_class();
    }

    /**
     * Method that checks if the element is valid, not empty. If !valid() it wont be rendered.
     * @since v1.0.7
     * @return boolean true for valid element, false otherwise.
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Method to create an empty fragment if isValid() is false in toDOMElement()
     * @param DOMDocument $document the document that will contain the empty element.
     * @see self::isValid().
     * @see self::toDOMElement().
     */
    protected function emptyElement($document)
    {
        $fragment = $document->createDocumentFragment();
        $fragment->appendChild($document->createTextNode(''));
        return $fragment;
    }
}
