<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Audio;
use Facebook\InstantArticles\Elements\Audible;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class AudioRule extends ConfigurationSelectorRule
{
    const PROPERTY_AUDIO_URL = 'audio.url';
    const PROPERTY_AUDIO_TITLE = 'audio.title';
    const PROPERTY_AUDIO_AUTOPLAY = 'audio.autoplay';
    const PROPERTY_AUDIO_MUTED = 'audio.muted';

    public function getContextClass()
    {
        return Audible::getClassName();
    }

    public static function create()
    {
        return new AudioRule();
    }

    public static function createFrom($configuration)
    {
        $audio_rule = self::create();
        $audio_rule->withSelector($configuration['selector']);

        $audio_rule->withProperties(
            [
                self::PROPERTY_AUDIO_URL,
                self::PROPERTY_AUDIO_TITLE,
                self::PROPERTY_AUDIO_AUTOPLAY,
                self::PROPERTY_AUDIO_MUTED
            ],
            $configuration
        );

        return $audio_rule;
    }

    public function apply($transformer, $audible, $node)
    {
        $audio = Audio::create();

        // Builds the image
        $url = $this->getProperty(self::PROPERTY_AUDIO_URL, $node);
        $title = $this->getProperty(self::PROPERTY_AUDIO_TITLE, $node);
        $autoplay = $this->getProperty(self::PROPERTY_AUDIO_AUTOPLAY, $node);
        $muted = $this->getProperty(self::PROPERTY_AUDIO_MUTED, $node);

        if ($url) {
            $audio->withURL($url);
            $audible->withAudio($audio);
        } else {
            // URL is a required field for Audio
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_AUDIO_URL,
                    $audible,
                    $node,
                    $this
                )
            );
        }

        if ($title) {
            $audio->withTitle($title);
        }
        if ($autoplay === "" || $autoplay === "true" || $autoplay === "autoplay") {
            $audio->enableAutoplay();
        }
        if ($muted === "" || $muted === "true" || $muted === "muted") {
            $audio->enableMuted();
        }

        return $audible;
    }
}
