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
 * An audio within for the article.
 * Also consider to use one of the other media types for an article:
 * <ul>
 *     <li>Image</li>
 *     <li>Video</li>
 *     <li>SlideShow</li>
 *     <li>Map</li>
 * </ul>.
 *
 * Example:
 *    <audio title="audio title">
 *        <source src="http://foo.com/mp3">
 *    </audio>
 *
 * @see Image
 * @see Video
 * @see SlideShow
 * @see Map
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/audio}
 */
class Audio extends Element
{
    /**
     * @var string The audio title
     */
    private $title;

    /**
     * @var string The string url for the audio file
     */
    private $url;

    /**
     * @var string Can be set with: empty ("") (Default), "muted" or "autoplay"
     */
    private $playback;

    /**
     * @var boolean stores the usage or not of autoplay for audio
     */
    private $autoplay;

    /**
     * @var boolean stores status of muted for this audio
     */
    private $muted;

    private function __construct()
    {
    }

    /**
     * @return Audio
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the URL for the audio. It is REQUIRED.
     *
     * @param string $url The url of image. Ie: http://domain.com/audiofile.mp3
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
     * The audio title.
     *
     * @param string $title the audio title that will be shown
     *
     * @return $this
     */
    public function withTitle($title)
    {
        Type::enforce($title, Type::STRING);
        $this->title = $title;

        return $this;
    }

    /**
     * It will make audio start automatically.
     *
     * @return $this
     */
    public function enableAutoplay()
    {
        $this->autoplay = true;

        return $this;
    }

    /**
     * It will make audio *NOT* start automatically.
     *
     * @return $this
     */
    public function disableAutoplay()
    {
        $this->autoplay = false;

        return $this;
    }

    /**
     * It will make audio be muted initially.
     *
     * @return $this
     */
    public function enableMuted()
    {
        $this->muted = true;

        return $this;
    }

    /**
     * It will make audio laud.
     *
     * @return $this
     */
    public function disableMuted()
    {
        $this->muted = false;

        return $this;
    }

    /**
     * Gets the audio title
     *
     * @return string Audio title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the url for the audio
     *
     * @return string Audio url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Gets the playback definition
     *
     * @return string playback definition
     */
    public function getPlayback()
    {
        return $this->playback;
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

        $element = $document->createElement('audio');

        // title markup optional
        if ($this->title) {
            $element->setAttribute('title', $this->title);
        }

        // Autoplay mode markup optional
        if ($this->autoplay) {
            $element->setAttribute('autoplay', 'autoplay');
        }

        // Autoplay mode markup optional
        if ($this->muted) {
            $element->setAttribute('muted', 'muted');
        }

        // Audio URL markup. REQUIRED
        if ($this->url) {
            $source_element = $document->createElement('source');
            $source_element->setAttribute('src', $this->url);
            $element->appendChild($source_element);
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Audio that contains not empty url, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->url);
    }
}
