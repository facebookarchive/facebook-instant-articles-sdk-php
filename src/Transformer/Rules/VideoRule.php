<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Symfony\Component\CssSelector\CssSelectorConverter;

use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class VideoRule extends ConfigurationSelectorRule
{
    const PROPERTY_VIDEO_URL = 'video.url';
    const PROPERTY_VIDEO_TYPE = 'video.type';
    const PROPERTY_PLAYBACK_MODE = 'video.playback';
    const PROPERTY_CONTROLS = 'video.controls';
    const PROPERTY_LIKE = 'video.like';
    const PROPERTY_COMMENTS = 'video.comments';

    /**
     * @var string
     */
    private $childSelector;

    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new VideoRule();
    }

    public function withContainsChild($child_selector)
    {
        $this->childSelector = $child_selector;
        return $this;
    }

    public function matchesNode($node)
    {
        $matches_node = parent::matchesNode($node);
        if ($matches_node && $this->childSelector) {
            $matches_node = false;
            if ($node->hasChildNodes()) {
                foreach ($node->childNodes as $child) {
                    $domXPath = new \DOMXPath($child->ownerDocument);
                    $converter = new CssSelectorConverter();
                    $xpath = $converter->toXPath($this->childSelector);
                    $results = $domXPath->query($xpath, $node);
                    foreach ($results as $result) {
                        if ($result === $child) {
                            $matches_node = true;
                        }
                    }
                }
            }
        }

        return $matches_node;
    }

    public static function createFrom($configuration)
    {
        $video_rule = self::create();
        $video_rule->withSelector($configuration['selector']);

        if (isset($configuration['containsChild'])) {
            $video_rule->withContainsChild($configuration['containsChild']);
        }

        $video_rule->withProperties(
            [
                self::PROPERTY_VIDEO_URL,
                self::PROPERTY_VIDEO_TYPE,

                Video::ASPECT_FIT,
                Video::ASPECT_FIT_ONLY,
                Video::FULLSCREEN,
                Video::NON_INTERACTIVE,

                self::PROPERTY_PLAYBACK_MODE,
                self::PROPERTY_CONTROLS,

                self::PROPERTY_LIKE,
                self::PROPERTY_COMMENTS
            ],
            $configuration
        );
        return $video_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $video = Video::create();

        // Builds the image
        $url = $this->getProperty(self::PROPERTY_VIDEO_URL, $node);
        if ($url) {
            $video->withURL($url);
            $instant_article->addChild($video);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_VIDEO_URL,
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        $video_type = $this->getProperty(self::PROPERTY_VIDEO_TYPE, $node);
        if ($video_type) {
            $video->withContentType($video_type);
        }

        if ($this->getProperty(Video::ASPECT_FIT, $node)) {
            $video->withPresentation(Video::ASPECT_FIT);
        } elseif ($this->getProperty(Video::ASPECT_FIT_ONLY, $node)) {
            $video->withPresentation(Video::ASPECT_FIT_ONLY);
        } elseif ($this->getProperty(Video::FULLSCREEN, $node)) {
            $video->withPresentation(Video::FULLSCREEN);
        } elseif ($this->getProperty(Video::NON_INTERACTIVE, $node)) {
            $video->withPresentation(Video::NON_INTERACTIVE);
        }

        if ($this->getProperty(self::PROPERTY_CONTROLS, $node)) {
            $video->enableControls();
        }

        if ($this->getProperty(self::PROPERTY_PLAYBACK_MODE, $node)) {
            $video->disableAutoplay();
        }

        if ($this->getProperty(self::PROPERTY_LIKE, $node)) {
            $video->enableLike();
        }

        if ($this->getProperty(self::PROPERTY_COMMENTS, $node)) {
            $video->enableComments();
        }

        $suppress_warnings = $transformer->suppress_warnings;
        $transformer->suppress_warnings = true;
        $transformer->transform($video, $node);
        $transformer->suppress_warnings = $suppress_warnings;

        return $instant_article;
    }
}
