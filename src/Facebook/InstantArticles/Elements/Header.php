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
 * The header of the article. A header can hold an Image,
 * Title, Authors and Dates for publishing and modification of the article.
 *
 * <header>
 *     <figure>
 *         <ui:image src={$this->getHeroBackground()} />
 *     </figure>
 *     <h1>{$this->name}</h1>
 *     <address>
 *         <a rel="facebook" href="http://facebook.com/everton.rosario">Everton</a>
 *         Everton Rosario is a passionate mountain biker on Facebook
 *     </address>
 *     <time
 *         class="op-published"
 *         datetime={date('c', $this->time)}>
 *         {date('F jS, g:ia', $this->time)}
 *     </time>
 *     <time
 *         class="op-modified"
 *         datetime={date('c', $last_update)}>
 *         {date('F jS, g:ia', $last_update)}
 *     </time>
 * </header>
 */
class Header extends Element
{
    /**
     * @var Image|Video|null for the image or video on the header.
     *
     * @see Image
     * @see Video
     */
    private $cover;

    /**
     * string|H1 The title of the Article that will be displayed on header.
     */
    private $title;

    /**
     * string|H2 The subtitle of the Article that will be displayed on header.
     */
    private $subtitle;

    /**
     * @var Author[] Authors of the article.
     */
    private $authors = [];

    /**
     * @var Time of publishing for the article
     */
    private $published;

    /**
     * @var Time of modification of the article, if it has
     * updated.
     */
    private $modified;

    /**
     * @var string Header kicker
     */
    private $kicker;

    /**
     * @var Ad[] Ads of the article.
     */
    private $ads = [];

    private function __construct()
    {
    }

    public static function create()
    {
        return new self();
    }

    /**
     * Sets the cover of InstantArticle with Image or Video
     * @param Image|Video $cover The cover for the header of the InstantArticle
     */
    public function withCover($cover)
    {
        Type::enforce($cover, [Image::getClassName(), Video::getClassName()]);
        $this->cover = $cover;

        return $this;
    }

    /**
     * Sets the title of InstantArticle
     * @param string|H1 $title The title of the InstantArticle
     */
    public function withTitle($title)
    {
        Type::enforce($title, array(Type::STRING, H1::getClassName()));
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the subtitle of InstantArticle
     * @param string|H2 $subtitle The subtitle of the InstantArticle
     */
    public function withSubTitle($subtitle)
    {
        Type::enforce($subtitle, array(Type::STRING, H2::getClassName()));
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Append another author to the article
     * @param Author $author The author name
     */
    public function addAuthor($author)
    {
        Type::enforce($author, Author::getClassName());
        $this->authors[] = $author;

        return $this;
    }

    /**
     * Replace all authors within this Article
     * @param array<Author> $authors All the authors
     */
    public function withAuthors($authors)
    {
        Type::enforceArrayOf($authors, Author::getClassName());
        $this->authors = $authors;

        return $this;
    }

    /**
     * Sets the publish Time for this article. REQUIRED
     * @param Time $published The time and date of publishing of this article. REQUIRED
     */
    public function withPublishTime($published)
    {
        Type::enforce($published, Time::getClassName());
        $this->published = $published;

        return $this;
    }

    /**
     * Sets the update Time for this article. Optional
     * @param Time $modified The time and date that this article was modified. Optional
     */
    public function withModifyTime($modified)
    {
        Type::enforce($modified, Time::getClassName());
        $this->modified = $modified;

        return $this;
    }

    /**
     * Sets the update Time for this article. Optional
     * @param Time $modified The time and date that this article was modified. Optional
     */
    public function withTime($time)
    {
        Type::enforce($time, Time::getClassName());
        if ($time->getType() === Time::MODIFIED) {
            $this->withModifyTime($time);
        } else {
            $this->withPublishTime($time);
        }

        return $this;
    }

    /**
     * Kicker text for the article header.
     * @param string The kicker text to be set
     */
    public function withKicker($kicker)
    {
        Type::enforce($kicker, Type::STRING);
        $this->kicker = $kicker;

        return $this;
    }

    /**
     * Append another ad to the article
     * @param Ad $ad Code for displaying an ad
     */
    public function addAd($ad)
    {
        Type::enforce($ad, Ad::getClassName());
        $this->ads[] = $ad;

        return $this;
    }

    /**
     * Replace all ads within this Article
     * @param array<Ad> $ads All the ads
     */
    public function withAds($ads)
    {
        Type::enforceArrayOf($ads, Ad::getClassName());
        $this->ads = $ads;

        return $this;
    }

    /**
     * @return Image|Video $cover The cover for the header of the InstantArticle
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @return string|H1 $title The title of the InstantArticle
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|H2 $subtitle The subtitle of the InstantArticle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return array<Author> $authors All the authors
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return Time $published The time and date of publishing of this article
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @return Time $modified The time and date that this article was modified.
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @return string The kicker text to be set
     */
    public function getKicker()
    {
        return $this->kicker;
    }

    /**
     * @return array<Ad> $ads All the ads
     */
    public function getAds()
    {
        return $this->ads;
    }

    /**
     * Structure and create the full ArticleImage in a XML format DOMElement.
     *
     * @param $document DOMDocument where this element will be appended. Optional
     */
    public function toDOMElement($document = null)
    {
        if (!$document) {
            $document = new \DOMDocument();
        }
        $element = $document->createElement('header');

        if ($this->cover) {
            $element->appendChild($this->cover->toDOMElement($document));
        }

        if ($this->title) {
            if (Type::is($this->title, Type::STRING)) {
                $title_element = $document->createElement('h1');
                $title_element->appendChild($document->createTextNode($this->title));
                $element->appendChild($title_element);
            } else {
                $element->appendChild($this->title->toDOMElement($document));
            }
        }

        if ($this->subtitle) {
            if (Type::is($this->subtitle, Type::STRING)) {
                $sub_title_element = $document->createElement('h2');
                $sub_title_element->appendChild($document->createTextNode($this->subtitle));
                $element->appendChild($sub_title_element);
            } else {
                $element->appendChild($this->subtitle->toDOMElement($document));
            }
        }

        if ($this->published) {
            $published_element = $this->published->toDOMElement($document);
            $element->appendChild($published_element);
        }

        if ($this->modified) {
            $modified_element = $this->modified->toDOMElement($document);
            $element->appendChild($modified_element);
        }

        if ($this->authors) {
            foreach ($this->authors as $author) {
                $element->appendChild($author->toDOMElement($document));
            }
        }

        if ($this->kicker) {
            $kicker_element = $document->createElement('h3');
            $kicker_element->setAttribute('class', 'op-kicker');
            $kicker_element->appendChild($document->createTextNode($this->kicker));
            $element->appendChild($kicker_element);
        }

        if (count($this->ads) === 1) {
            $this->ads[0]->disableDefaultForReuse();
            $element->appendChild($this->ads[0]->toDOMElement($document));
        } elseif (count($this->ads) >= 2) {
            $ads_container = $document->createElement('section');
            $ads_container->setAttribute('class', 'op-ad-template');

            $default_is_set = false;
            foreach ($this->ads as $ad) {
                if ($default_is_set) {
                    $ad->disableDefaultForReuse();
                }

                if ($ad->getIsDefaultForReuse()) {
                    $default_is_set = true;
                }

                $ads_container->appendChild($ad->toDOMElement($document));
            }
            $element->appendChild($ads_container);
        }

        return $element;
    }
}
