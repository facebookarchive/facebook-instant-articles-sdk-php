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
class Header extends Element implements ChildrenContainer
{
    /**
     * @var Image|Video|Slideshow|null for the image or video on the header.
     *
     * @see Image
     * @see Slideshow
     * @see Video
     */
    private $cover;

    /**
     * H1 The title of the Article that will be displayed on header.
     */
    private $title;

    /**
     * H2 The subtitle of the Article that will be displayed on header.
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
     * @var H3 Header kicker
     */
    private $kicker;

    /**
     * @var Ad[] Ads of the article.
     */
    private $ads = [];

    /**
     * @var Sponsor The sponsor for this article. See Branded Content.
     */
    private $sponsor;

    private function __construct()
    {
    }

    /**
     * @return Header
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the cover of InstantArticle with Image or Video
     *
     * @param Image|Video|Slideshow $cover The cover for the header of the InstantArticle
     *
     * @return $this
     */
    public function withCover($cover)
    {
        Type::enforce(
            $cover,
            [
                Image::getClassName(),
                Slideshow::getClassName(),
                Video::getClassName()
            ]
        );
        $this->cover = $cover;

        return $this;
    }

    /**
     * Sets the title of InstantArticle
     *
     * @param string|H1 $title The title of the InstantArticle
     *
     * @return $this
     */
    public function withTitle($title)
    {
        Type::enforce($title, array(Type::STRING, H1::getClassName()));
        if (Type::is($title, Type::STRING)) {
            $this->title = H1::create()->appendText($title);
        } else {
            $this->title = $title;
        }

        return $this;
    }

    /**
     * Sets the subtitle of InstantArticle
     *
     * @param string|H2 $subtitle The subtitle of the InstantArticle
     *
     * @return $this
     */
    public function withSubTitle($subtitle)
    {
        Type::enforce($subtitle, array(Type::STRING, H2::getClassName()));
        if (Type::is($subtitle, Type::STRING)) {
            $this->subtitle = H2::create()->appendText($subtitle);
        } else {
            $this->subtitle = $subtitle;
        }


        return $this;
    }

    /**
     * Append another author to the article
     *
     * @param Author $author The author name
     *
     * @return $this
     */
    public function addAuthor($author)
    {
        Type::enforce($author, Author::getClassName());
        $this->authors[] = $author;

        return $this;
    }

    /**
     * Replace all authors within this Article
     *
     * @param Author[] $authors All the authors
     *
     * @return $this
     */
    public function withAuthors($authors)
    {
        Type::enforceArrayOf($authors, Author::getClassName());
        $this->authors = $authors;

        return $this;
    }

    /**
     * Sets the publish Time for this article. REQUIRED
     *
     * @param Time $published The time and date of publishing of this article. REQUIRED
     *
     * @return $this
     */
    public function withPublishTime($published)
    {
        Type::enforce($published, Time::getClassName());
        $this->published = $published;

        return $this;
    }

    /**
     * Sets the update Time for this article. Optional
     *
     * @param Time $modified The time and date that this article was modified. Optional
     *
     * @return $this
     */
    public function withModifyTime($modified)
    {
        Type::enforce($modified, Time::getClassName());
        $this->modified = $modified;

        return $this;
    }

    /**
     * Sets the update Time for this article. Optional
     *
     * @param Time $time The time and date that this article was modified. Optional
     *
     * @return $this
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
     *
     * @param H3|string The kicker text to be set
     *
     * @return $this
     */
    public function withKicker($kicker)
    {
        Type::enforce($kicker, array(Type::STRING, H3::getClassName()));
        if (Type::is($kicker, Type::STRING)) {
            $this->kicker = H3::create()->appendText($kicker);
        } else {
            $this->kicker = $kicker;
        }

        return $this;
    }

    /**
     * Append another ad to the article
     *
     * @param Ad $ad Code for displaying an ad
     *
     * @return $this
     */
    public function addAd($ad)
    {
        Type::enforce($ad, Ad::getClassName());
        $this->ads[] = $ad;

        return $this;
    }

    /**
     * Replace all ads within this Article
     *
     * @param Ad[] $ads All the ads
     *
     * @return $this
     */
    public function withAds($ads)
    {
        Type::enforceArrayOf($ads, Ad::getClassName());
        $this->ads = $ads;

        return $this;
    }

    /**
     * Sets the sponsor for this Article.
     *
     * @param Sponsor $sponsor The sponsor of article to be set.
     *
     * @return $this
     */
    public function withSponsor($sponsor)
    {
        Type::enforce($sponsor, Sponsor::getClassName());
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * @return Image|Slideshow|Video The cover for the header of the InstantArticle
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
     * @return Author[] All the authors
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return Time The time and date of publishing of this article
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @return Time The time and date that this article was modified.
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
     * @return Ad[] All the ads
     */
    public function getAds()
    {
        return $this->ads;
    }

    /**
     * @return Sponsor the sponsor of this Article.
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

    /**
     * Structure and create the full ArticleImage in a XML format DOMElement.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
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

        $element = $document->createElement('header');

        if ($this->cover && $this->cover->isValid()) {
            $element->appendChild($this->cover->toDOMElement($document));
        }

        if ($this->title && $this->title->isValid()) {
            $element->appendChild($this->title->toDOMElement($document));
        }

        if ($this->subtitle && $this->subtitle->isValid()) {
            $element->appendChild($this->subtitle->toDOMElement($document));
        }

        if ($this->published && $this->published->isValid()) {
            $published_element = $this->published->toDOMElement($document);
            $element->appendChild($published_element);
        }

        if ($this->modified && $this->modified->isValid()) {
            $modified_element = $this->modified->toDOMElement($document);
            $element->appendChild($modified_element);
        }

        if ($this->authors) {
            foreach ($this->authors as $author) {
                if ($author->isValid()) {
                    $element->appendChild($author->toDOMElement($document));
                }
            }
        }

        if ($this->kicker && $this->kicker->isValid()) {
            $kicker_element = $this->kicker->toDOMElement($document);
            $kicker_element->setAttribute('class', 'op-kicker');
            $element->appendChild($kicker_element);
        }

        if (count($this->ads) === 1) {
            $this->ads[0]->disableDefaultForReuse();
            if ($this->ads[0]->isValid()) {
                $element->appendChild($this->ads[0]->toDOMElement($document));
            }
        } elseif (count($this->ads) >= 2) {
            $ads_container = $document->createElement('section');
            $ads_container->setAttribute('class', 'op-ad-template');

            $default_is_set = false;
            $has_valid_ad = false;
            foreach ($this->ads as $ad) {
                if ($default_is_set) {
                    $ad->disableDefaultForReuse();
                }

                if ($ad->getIsDefaultForReuse()) {
                    $default_is_set = true;
                }

                if ($ad->isValid()) {
                    $ads_container->appendChild($ad->toDOMElement($document));
                    $has_valid_ad = true;
                }
            }
            if ($has_valid_ad) {
                $element->appendChild($ads_container);
            }
        }

        if ($this->sponsor && $this->sponsor->isValid()) {
            $element->appendChild($this->sponsor->toDOMElement($document));
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid tag, false otherwise.
     */
    public function isValid()
    {
        $has_ad = count($this->ads) > 0;
        $has_valid_ad = false;
        if ($has_ad) {
            foreach ($this->ads as $ad) {
                if ($ad->isValid()) {
                    $has_valid_ad = true;
                    break;
                }
            }
        }
        return
            ($this->title && $this->title->isValid()) ||
             $has_valid_ad;
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return array of Elements contained by Header.
     */
    public function getContainerChildren()
    {
        $children = array();

        if ($this->cover) {
            $children[] = $this->cover;
        }

        if ($this->title) {
            $children[] = $this->title;
        }

        if ($this->subtitle) {
            $children[] = $this->subtitle;
        }

        if ($this->published) {
            $children[] = $this->published;
        }

        if ($this->modified) {
            $children[] = $this->modified;
        }

        if ($this->authors) {
            foreach ($this->authors as $author) {
                $children[] = $author;
            }
        }

        if ($this->kicker) {
            $children[] = $this->kicker;
        }

        if (count($this->ads) > 0) {
            foreach ($this->ads as $ad) {
                $children[] = $ad;
            }
        }

        if ($this->sponsor) {
            $children[] = $this->sponsor;
        }

        return $children;
    }
}
