<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules\Compat;

use Facebook\InstantArticles\Transformer\Rules\ConfigurationSelectorRule;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Slideshow;

class JetpackSlideshowRule extends ConfigurationSelectorRule
{
    const PROPERTY_JETPACK_DATA_GALLERY = 'jetpack.data-gallery';

    public function getContextClass()
    {
        return InstantArticle::getClassName();
    }

    public static function create()
    {
        return new JetpackSlideshowRule();
    }

    public static function createFrom($configuration)
    {
        $slideshow_rule = self::create();
        $slideshow_rule->withSelector($configuration['selector']);

        $slideshow_rule->withProperties(
            [
                self::PROPERTY_JETPACK_DATA_GALLERY
            ],
            $configuration
        );

        return $slideshow_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        // Builds the slideshow
        $slideshow = Slideshow::create();
        $instant_article->addChild($slideshow);

        $gallery = $this->getProperty(self::PROPERTY_JETPACK_DATA_GALLERY, $node);

        if ($gallery && isset($gallery)) {
            foreach ($gallery as $gallery_image) {
                // Constructs Image if it contains URL
                if (!Type::isTextEmpty($gallery_image['src'])) {
                    $image = Image::create();
                    $image->withURL($gallery_image['src']);

                    // Constructs Caption element when present in the JSON
                    if (!Type::isTextEmpty($gallery_image['caption'])) {
                        $caption = Caption::create();
                        $caption->appendText($gallery_image['caption']);
                        $image->withCaption($caption);
                    }
                    $slideshow->addImage($image);
                }
            }
        }

        $transformer->transform($slideshow, $node);

        return $instant_article;
    }
}
