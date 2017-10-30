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
  * Class InstantArticle
  * This class holds the content of one InstantArticle and is children
  *
  *    <html>
  *        <head>
  *            ...
  *        </head>
  *        <body>
  *            <article>
  *                <header>
  *                    <figure>...</figure>
  *                    <h1>...</h1>
  *                    <time>...</time>
  *                </header>
  *                <contents...>
  *            </article>
  *        </body>
  *    </html>
  *
*/

class InstantArticle extends Element implements ChildrenContainer, InstantArticleInterface
{
    const CURRENT_VERSION = '1.6.2';

    /**
     * The meta properties that are used on <head>
     */
    private $metaProperties = [];

    /**
     * @var string The canonical URL for the Instant Article
     */
    private $canonicalURL;

    /**
     * @var string The markup version for this InstantArticle
     */
    private $markupVersion = 'v1.0';

    /**
     * @var boolean The ad strategy that will be used. True by default
     */
    private $isAutomaticAdPlaced = true;
    
    /**
     * @var string The ad density that will be used. "default" by default
     */
    private $adDensity = 'default';

    /**
     * @var boolean The ad strategy that will be used. False by default
     */
    private $isRecirculationAdPlaced = false;

    /**
     * @var string The ad placement strategy that will be used. Optional
     */
    private $adRecirculationPlacement;
    
    /**
     * @var string The charset that will be used. "utf-8" by default.
     */
    private $charset = 'utf-8';

    /**
     * @var string|null The style that will be applied to the article. Optional.
     */
    private $style;

    /**
     * @var Header element to hold header content, like images etc
     */
    private $header;

    /**
     * @var Footer element to hold footer content.
     */
    private $footer;

    /**
     * @var Element[] of all elements an article can have.
     */
    private $children = [];

    /**
     * @var boolean flag that indicates if this article is Right-to-left(RTL). Defaults to false.
     */
    private $isRTLEnabled = false;

    /**
     * Factory method
     * @return InstantArticle object.
     */
    public static function create()
    {
        return new InstantArticle();
    }

    /**
     * Private constructor. It must be used the Factory method
     * @see InstantArticle#create() For building objects
     */
    private function __construct()
    {
        $this->header = Header::create();
        $this->addMetaProperty('op:generator', 'facebook-instant-articles-sdk-php');
        $this->addMetaProperty('op:generator:version', self::CURRENT_VERSION);
    }

    /**
     * Sets the canonical URL for the Instant Article. It is REQUIRED.
     *
     * @param string $url The canonical url of article. Ie: http://domain.com/article.html
     *
     * @return $this
     */
    public function withCanonicalURL($url)
    {
        Type::enforce($url, Type::STRING);
        $this->canonicalURL = $url;

        return $this;
    }

    /**
     * Sets the charset for the Instant Article. utf-8 by default.
     *
     * @param string $charset The charset of article. Ie: "iso-8859-1"
     *
     * @return $this
     */
    public function withCharset($charset)
    {
        Type::enforce($charset, Type::STRING);
        $this->charset = $charset;

        return $this;
    }

    /**
     * Sets the style to be applied to this Instant Article
     *
     * @param string $style Name of the style
     *
     * @return $this
     */
    public function withStyle($style)
    {
        Type::enforce($style, Type::STRING);
        $this->style = $style;

        return $this;
    }

    /**
     * Use the strategy of auto ad placement
     */
    public function enableAutomaticAdPlacement()
    {
        $this->isAutomaticAdPlaced = true;
        return $this;
    }

    /**
     * Use the strategy of manual ad placement
     */
    public function disableAutomaticAdPlacement()
    {
        $this->isAutomaticAdPlaced = false;
        return $this;
    }

    /**
     * Use the strategy of auto recirculation ad placement
     */
    public function disableAutomaticRecirculationPlacement()
    {
        $this->isRecirculationAdPlaced = false;
        return $this;
    }

    /**
     * Use the strategy of manual recirculation ad placement
     */
    public function enableAutomaticRecirculationPlacement()
    {
        $this->isRecirculationAdPlaced = true;
        return $this;
    }

    /**
     * Sets the recirculation ad placement to be applied to this Instant Article
     * 
     * @param string $adRecirculationPlacement Ad placement
     * 
     * @return $this
     */
    public function withRecirculationPlacement($adRecirculationPlacement)
    {
        $this->adRecirculationPlacement = $adRecirculationPlacement;
        return $this;
    }

    /**
     * Sets the ad density to be used for auto ad placement
     *
     * @param string $adDensity Ad density
     *
     * @return $this
     */
    public function withAdDensity($adDensity)
    {
        Type::enforce($adDensity, Type::STRING);
        $this->adDensity = $adDensity;

        return $this;
    }

    /**
     * Updates article to use RTL orientation.
     */
    public function enableRTL()
    {
        $this->isRTLEnabled = true;
        return $this;
    }

    /**
     * Updates article to use LTR orientation (default), disabling RTL.
     */
    public function disableRTL()
    {
        $this->isRTLEnabled = false;
        return $this;
    }

    /**
     * Sets the header content to this InstantArticle
     *
     * @param Header $header to be added to this Article.
     *
     * @return $this
     */
    public function withHeader($header)
    {
        Type::enforce($header, Header::getClassName());
        $this->header = $header;

        return $this;
    }

    /**
     * Sets the footer content to this InstantArticle
     *
     * @param Footer $footer to be added to this Article.
     *
     * @return $this
     */
    public function withFooter($footer)
    {
        Type::enforce($footer, Footer::getClassName());
        $this->footer = $footer;

        return $this;
    }

    /**
     * Replace all the children within this InstantArticle
     *
     * @param Element[] $children Array of elements replacing the original.
     *
     * @return $this
     */
    public function withChildren($children)
    {
        Type::enforceArrayOf(
            $children,
            [
                Ad::getClassName(),
                Analytics::getClassName(),
                AnimatedGIF::getClassName(),
                Audio::getClassName(),
                Blockquote::getClassName(),
                Image::getClassName(),
                H1::getClassName(),
                H2::getClassName(),
                Interactive::getClassName(),
                ListElement::getClassName(),
                Map::getClassName(),
                Paragraph::getClassName(),
                Pullquote::getClassName(),
                RelatedArticles::getClassName(),
                Slideshow::getClassName(),
                SocialEmbed::getClassName(),
                Video::getClassName()
            ]
        );
        $this->children = $children;

        return $this;
    }

    /**
     * Replace all the children within this InstantArticle
     *
     * @param Type::INTEGER $index The index of the element to be deleted
     *                             in the array of children.
     *
     * @return $this
     */
    public function deleteChild($index)
    {
        Type::enforce($index, Type::INTEGER);
        $children = [];
        foreach ($this->children as $childIndex => $child) {
            if ($childIndex != $index) {
                $children[] = $child;
            }
        }
        $this->children = $children;

        return $this;
    }

    /**
     * Adds new child elements to this InstantArticle
     *
     * @param Element $child to be added to this Article.
     *
     * @return $this
     */
    public function addChild($child)
    {
        Type::enforce(
            $child,
            [
                Ad::getClassName(),
                Analytics::getClassName(),
                AnimatedGIF::getClassName(),
                Audio::getClassName(),
                Blockquote::getClassName(),
                Image::getClassName(),
                H1::getClassName(),
                H2::getClassName(),
                Interactive::getClassName(),
                ListElement::getClassName(),
                Map::getClassName(),
                Paragraph::getClassName(),
                Pullquote::getClassName(),
                RelatedArticles::getClassName(),
                Slideshow::getClassName(),
                SocialEmbed::getClassName(),
                Video::getClassName()
            ]
        );
        $this->children[] = $child;

        return $this;
    }

    /**
     * Adds new child elements to the front of this InstantArticle
     *
     * @param Element to be added to this Article.
     *
     * @return $this
     */
    public function unshiftChild($child)
    {
        Type::enforce(
            $child,
            [
                Ad::getClassName(),
                Analytics::getClassName(),
                AnimatedGIF::getClassName(),
                Audio::getClassName(),
                Blockquote::getClassName(),
                Image::getClassName(),
                H1::getClassName(),
                H2::getClassName(),
                Interactive::getClassName(),
                ListElement::getClassName(),
                Map::getClassName(),
                Paragraph::getClassName(),
                Pullquote::getClassName(),
                RelatedArticles::getClassName(),
                Slideshow::getClassName(),
                SocialEmbed::getClassName(),
                Video::getClassName()
            ]
        );
        array_unshift($this->children, $child);

        return $this;
    }

    /**
     * @return string canonicalURL from the InstantArticle
     */
    public function getCanonicalURL()
    {
        return $this->canonicalURL;
    }

    /**
     * @return string style from the InstantArticle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @return Header header element from the InstantArticle
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return Footer footer element from the InstantArticle
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @return array<Element> the elements this article contains
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return boolean if this article is Right-to-left(RTL).
     */
    public function isRTLEnabled()
    {
        return $this->isRTLEnabled;
    }

    /**
     * @return string The article charset.
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Adds a meta property for the <head> of Instant Article.
     *
     * @param string $property_name name of meta attribute
     * @param string $property_content content of meta attribute
     *
     * @return $this
     */
    public function addMetaProperty($property_name, $property_content)
    {
        $this->metaProperties[$property_name] = $property_content;
        return $this;
    }

    public function render($doctype = '<!doctype html>', $format = false)
    {
        $doctype = is_null($doctype) ? '<!doctype html>' : $doctype;
        return parent::render($doctype, $format);
    }

    public function toDOMElement($document = null)
    {
        if (!$document) {
            $document = new \DOMDocument();
        }

        // Builds and appends head to the HTML document
        $html = $document->createElement('html');
        if ($this->isRTLEnabled) {
            $html->setAttribute('dir', 'rtl');
        }
        $head = $document->createElement('head');
        $html->appendChild($head);

        $link = $document->createElement('link');
        $link->setAttribute('rel', 'canonical');
        $link->setAttribute('href', $this->canonicalURL);
        $head->appendChild($link);

        $charset = $document->createElement('meta');
        $charset->setAttribute('charset', $this->charset);
        $head->appendChild($charset);

        $this->addMetaProperty('op:markup_version', $this->markupVersion);
        if ($this->header && count($this->header->getAds()) > 0) {
            if ($this->isAutomaticAdPlaced) {
                $this->addMetaProperty(
                    'fb:use_automatic_ad_placement',
                    'enable=true ad_density=' . $this->adDensity
                );
            } else {
                $this->addMetaProperty('fb:use_automatic_ad_placement', 'false');
            }
        }

        if ($this->header && $this->isRecirculationAdPlaced && $this->adRecirculationPlacement) {
            $this->addMetaProperty(
                'fb:op-recirculation-ads',
                $this->adRecirculationPlacement
            );
        }

        if ($this->style) {
            $this->addMetaProperty('fb:article_style', $this->style);
        }

        // Adds all meta properties
        foreach ($this->metaProperties as $property_name => $property_content) {
            $head->appendChild(
                $this->createMetaElement(
                    $document,
                    $property_name,
                    $property_content
                )
            );
        }

        // Build and append body and article tags to the HTML document
        $body = $document->createElement('body');
        $article = $document->createElement('article');
        $body->appendChild($article);
        $html->appendChild($body);
        if ($this->header && $this->header->isValid()) {
            $article->appendChild($this->header->toDOMElement($document));
        }
        if ($this->children) {
            foreach ($this->children as $child) {
                if (Type::is($child, TextContainer::getClassName())) {
                    if (count($child->getTextChildren()) === 0) {
                        continue;
                    } elseif (count($child->getTextChildren()) === 1) {
                        if (Type::is($child->getTextChildren()[0], Type::STRING) &&
                            trim($child->getTextChildren()[0]) === '') {
                            continue;
                        }
                    }
                }
                $article->appendChild($child->toDOMElement($document));
            }
            if ($this->footer && $this->footer->isValid()) {
                $article->appendChild($this->footer->toDOMElement($document));
            }
        } else {
            $article->appendChild($document->createTextNode(''));
        }

        return $html;
    }

    private function createMetaElement($document, $property_name, $property_content)
    {
        $element = $document->createElement('meta');
        $element->setAttribute('property', $property_name);
        $element->setAttribute('content', $property_content);
        return $element;
    }

    public function isValid()
    {
        $header_valid = false;
        if ($this->getHeader()) {
            $header_valid = $this->getHeader()->isValid();
        }

        $items = $this->getChildren();
        $one_item_valid = false;
        if ($items) {
            foreach ($items as $item) {
                if ($item->isValid()) {
                    $one_item_valid = true;
                    break;
                }
            }
        }

        $footer_valid = true;
        if ($this->getFooter()) {
            $footer_valid = $this->getFooter()->isValid();
        }

        return
            $this->canonicalURL &&
            !Type::isTextEmpty($this->canonicalURL) &&
            $header_valid &&
            $footer_valid &&
            $one_item_valid;
    }

    public function getContainerChildren()
    {
        $children = array();

        $header = $this->getHeader();
        if ($header) {
            $children[] = $header;
        }

        $items = $this->getChildren();
        if ($items) {
            foreach ($items as $item) {
                $children[] = $item;
            }
        }

        $footer = $this->getFooter();
        if ($footer) {
            $children[] = $footer;
        }

        return $children;
    }

    public function getFirstParagraph()
    {
        $items = $this->getChildren();
        if ($items) {
            foreach ($items as $item) {
                if (Type::is($item, Paragraph::getClassName())) {
                    return $item;
                }
            }
        }
        // Case no paragraph exists, we return an empty paragraph
        return Paragraph::create();
    }
}
