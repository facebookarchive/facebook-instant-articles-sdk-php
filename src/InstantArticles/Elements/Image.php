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
 * Class Image
 * This element Class is the image for the article.
 * Also consider to use one of the other media types for an article:
 * <ul>
 *     <li>Audio</li>
 *     <li>Video</li>
 *     <li>SlideShow</li>
 *     <li>Map</li>
 * </ul>.
 *
 * Example:
 *  <figure>
 *      <img src="http://mydomain.com/path/to/img.jpg" />
 *      <figcaption>This image is amazing</figcaption>
 *  </figure>
 *
 * @see Audio
 * @see Video
 * @see SlideShow
 * @see Map
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/image}
 */
class Image extends Audible implements ChildrenContainer
{
    const ASPECT_FIT = 'aspect-fit';
    const ASPECT_FIT_ONLY = 'aspect-fit-only';
    const FULLSCREEN = 'fullscreen';
    const NON_INTERACTIVE = 'non-interactive';

    /**
     * @var boolean marks if any created image will have likes enabled by default
     */
    public static $defaultLikeEnabled = false;

    /**
     * @var boolean marks if any created image will have comments enabled by default
     */
    public static $defaultCommentEnabled = false;

    /**
     * @var Caption The caption for Image
     */
    private $caption;

    /**
     * @var string The string url for the image hosted on web that will be shown
     * on the article
     */
    private $url;

    /**
     * @var bool Tells if like is enabled. Default: false
     */
    private $isLikeEnabled;

    /**
     * @var bool Tells if comments are enabled. Default: false
     */
    private $isCommentsEnabled;

    /**
     * @var string The picture size for the video.
     * @see Image::ASPECT_FIT
     * @see Image::ASPECT_FIT_ONLY
     * @see Image::FULLSCREEN
     * @see Image::NON_INTERACTIVE
     */
    private $presentation;

    /**
     * @var GeoTag The Map object
     */
    private $geoTag;

    /**
     * @var Audio The audio file for this Image
     */
    private $audio;

    /**
     * Private constructor.
     * @see Image::create();.
     */
    private function __construct()
    {
        $this->isLikeEnabled = self::$defaultLikeEnabled;
        $this->isCommentsEnabled = self::$defaultCommentEnabled;
    }

    /**
     * Factory method for the Image
     * @return Image the new instance
     */
    public static function create()
    {
        return new self();
    }

    /**
     * This sets figcaption tag as documentation. It overrides all sets
     * made with Caption.
     *
     * @param Caption $caption the caption the image will have
     *
     * @return $this
     */
    public function withCaption($caption)
    {
        Type::enforce($caption, Caption::getClassName());
        $this->caption = $caption;

        return $this;
    }

    /**
     * Sets the URL for the image. It is REQUIRED.
     *
     * @param string $url The url of image. Ie: http://domain.com/img.png
     *
     * @return $this
     */
    public function withURL($url)
    {
        Type::enforce($url, Type::STRING);
        $this->url = $url;

        return $this;
    }

    /**
     * Sets the aspect ration presentation for the video.
     *
     * @param string $presentation one of the constants ASPECT_FIT, ASPECT_FIT_ONLY, FULLSCREEN or NON_INTERACTIVE
     * @see Image::ASPECT_FIT
     * @see Image::ASPECT_FIT_ONLY
     * @see Image::FULLSCREEN
     * @see Image::NON_INTERACTIVE
     *
     * @return $this
     */
    public function withPresentation($presentation)
    {
        Type::enforceWithin(
            $presentation,
            [
                Image::ASPECT_FIT,
                Image::ASPECT_FIT_ONLY,
                Image::FULLSCREEN,
                Image::NON_INTERACTIVE
            ]
        );
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Makes like enabled for this image.
     */
    public function enableLike()
    {
        $this->isLikeEnabled = true;

        return $this;
    }

    /**
     * Makes like disabled for this image.
     */
    public function disableLike()
    {
        $this->isLikeEnabled = false;

        return $this;
    }

    /**
     * Makes comments enabled for this image.
     */
    public function enableComments()
    {
        $this->isCommentsEnabled = true;

        return $this;
    }

    /**
     * Makes comments disabled for this image.
     */
    public function disableComments()
    {
        $this->isCommentsEnabled = false;

        return $this;
    }

    /**
     * Sets the geotag on the image.
     *
     * @see {link:http://geojson.org/}
     */
    public function withGeoTag($geo_tag)
    {
        Type::enforce($geo_tag, [Type::STRING, GeoTag::getClassName()]);
        if (Type::is($geo_tag, Type::STRING)) {
            $this->geoTag = GeoTag::create()->withScript($geo_tag);
        } elseif (Type::is($geo_tag, GeoTag::getClassName())) {
            $this->geoTag = $geo_tag;
        }

        return $this;
    }

    /**
     * Adds audio to this image.
     *
     * @param Audio $audio The audio object
     *
     * @return $this
     */
    public function withAudio($audio)
    {
        Type::enforce($audio, Audio::getClassName());
        $this->audio = $audio;

        return $this;
    }

    /**
     * @return Caption gets the caption obj
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @return string URL gets the image url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return boolean tells if the like button is enabled
     */
    public function isLikeEnabled()
    {
        return $this->isLikeEnabled;
    }

    /**
     * @return boolean tells if the comments widget is enabled
     */
    public function isCommentsEnabled()
    {
        return $this->isCommentsEnabled;
    }

    /**
     * @return string one of the constants ASPECT_FIT, ASPECT_FIT_ONLY, FULLSCREEN or NON_INTERACTIVE
     * @see Image::ASPECT_FIT
     * @see Image::ASPECT_FIT_ONLY
     * @see Image::FULLSCREEN
     * @see Image::NON_INTERACTIVE
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * @return Map The json geotag content inside the script geotag
     */
    public function getGeotag()
    {
        return $this->geoTag;
    }

    /**
     * @return Audio the audio object
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * Structure and create the full Image in a XML format DOMElement.
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

        $element = $document->createElement('figure');

        // Like/comments markup optional
        if ($this->isLikeEnabled || $this->isCommentsEnabled) {
            if ($this->isLikeEnabled && $this->isCommentsEnabled) {
                $element->setAttribute('data-feedback', 'fb:likes,fb:comments');
            } elseif ($this->isLikeEnabled) {
                $element->setAttribute('data-feedback', 'fb:likes');
            } else {
                $element->setAttribute('data-feedback', 'fb:comments');
            }
        }

        // Presentation
        if ($this->presentation) {
            $element->setAttribute('data-mode', $this->presentation);
        }

        // URL markup required
        if ($this->url) {
            $image_element = $document->createElement('img');
            $image_element->setAttribute('src', $this->url);
            $element->appendChild($image_element);
        }

        // Caption markup optional
        if ($this->caption) {
            $element->appendChild($this->caption->toDOMElement($document));
        }

        // Geotag markup optional
        if ($this->geoTag) {
            $element->appendChild($this->geoTag->toDOMElement($document));
        }

        // Audio markup optional
        if ($this->audio) {
            $element->appendChild($this->audio->toDOMElement($document));
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Image that contains valid url, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->url);
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return array of Elements contained by Image.
     */
    public function getContainerChildren()
    {
        $children = array();

        if ($this->caption) {
            $children[] = $this->caption;
        }

        // Geotag markup optional
        if ($this->geoTag) {
            $children[] = $this->geoTag;
        }

        // Audio markup optional
        if ($this->audio) {
            $children[] = $this->audio;
        }

        return $children;
    }

    /**
     * Modify the default setup to enable/disable likes in images
     *
     * WARNING this is not Thread-safe, so if you are using pthreads or any other multithreaded engine,
     * this might not work as expected. (you will need to set this in all working threads manually)
     * @param boolean $enabled inform true to enable likes on images per default or false to disable like on images.
     */
    public static function setDefaultLikeEnabled($enabled)
    {
        self::$defaultLikeEnabled = $enabled;
    }

    /**
     * Modify the default setup to enable/disable comments in images
     *
     * WARNING this is not Thread-safe, so if you are using pthreads or any other multithreaded engine,
     * this might not work as expected. (you will need to set this in all working threads manually)
     * @param boolean $enabled inform true to enable comments on images per default or false to disable commenting on images.
     */
    public static function setDefaultCommentEnabled($enabled)
    {
        self::$defaultCommentEnabled = $enabled;
    }
}
