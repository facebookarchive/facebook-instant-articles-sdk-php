<?php
/**
 * Copyright 2014 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Validators\Type;

/**
 * Each blockquote of article should be an instance of this class.
 *
 * Example:
 * <blockquote> This is the first blockquote of body text. </blockquote>
 *
 * or
 *
 * <blockquote> This is the <i>second</i> blockquote of <b>body text</b>. </blockquote>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/blockquote}
 */
class Blockquote extends TextContainer
{

    /**
     * @var string $text the text content for blockquote
     */
    private $text;

    private function __construct()
    {
    }

    /**
     * @return Blockquote
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the unescaped text within the blockquote.
     *
     * @param string $text The unescaped string.
     *
     * @return $this
     */
    public function withText($text)
    {
        Type::enforce($text, Type::STRING);
        $this->text = $text;

        return $this;
    }

    /**
     * Structure and create the full Blockquote in a DOMElement.
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

        $element = $document->createElement('blockquote');

        $element->appendChild($this->textToDOMDocumentFragment($document));

        return $element;
    }
}
