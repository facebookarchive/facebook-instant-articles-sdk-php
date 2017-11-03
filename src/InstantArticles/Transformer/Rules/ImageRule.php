<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Cite;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Validators\Type;

class ImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';
    const PROPERTY_LIKE = 'image.like';
    const PROPERTY_COMMENTS = 'image.comments';
    const PROPERTY_CREDIT = 'image.credit';
    const PROPERTY_CAPTION = 'image.caption';

    const ASPECT_FIT = 'aspect-fit';
    const ASPECT_FIT_ONLY = 'aspect-fit-only';
    const FULLSCREEN = 'fullscreen';
    const NON_INTERACTIVE = 'non-interactive';


    public function getContextClass()
    {
        return
            [
                InstantArticle::getClassName(),
                Paragraph::getClassName()
            ];
    }

    public static function create()
    {
        return new static();
    }

    public static function createFrom($configuration)
    {
        $image_rule = static::create();
        $image_rule->withSelector($configuration['selector']);

        $image_rule->withProperties(
            [
                self::PROPERTY_IMAGE_URL,
                self::PROPERTY_LIKE,
                self::PROPERTY_COMMENTS,
                self::PROPERTY_CREDIT,
                self::PROPERTY_CAPTION,
                self::ASPECT_FIT,
                self::ASPECT_FIT_ONLY,
                self::FULLSCREEN,
                self::NON_INTERACTIVE
            ],
            $configuration
        );

        return $image_rule;
    }

    public function apply($transformer, $context, $node)
    {
        $image = Image::create();

        if (Type::is($context, InstantArticle::getClassName())) {
            $instant_article = $context;
        } elseif ($transformer->getInstantArticle()) {
            $instant_article = $transformer->getInstantArticle();
            $context->disableEmptyValidation();
            $context = Paragraph::create();
            $context->disableEmptyValidation();
        } else {
            $transformer->addWarning(
                // This new error message should be something like:
                // Could not transform Image, as no root InstantArticle was provided.
                new NoRootInstantArticleFoundWarning(null, $node)
            );
            return $context;
        }

        // Builds the image
        $url = $this->getProperty(self::PROPERTY_IMAGE_URL, $node);
        if ($url) {
            $image->withURL($url);
            $instant_article->addChild($image);
            if ($instant_article !== $context) {
                $instant_article->addChild($context);
            }
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IMAGE_URL,
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        if ($this->getProperty(Image::ASPECT_FIT, $node)) {
            $image->withPresentation(Image::ASPECT_FIT);
        } elseif ($this->getProperty(Image::ASPECT_FIT_ONLY, $node)) {
            $image->withPresentation(Image::ASPECT_FIT_ONLY);
        } elseif ($this->getProperty(Image::FULLSCREEN, $node)) {
            $image->withPresentation(Image::FULLSCREEN);
        } elseif ($this->getProperty(Image::NON_INTERACTIVE, $node)) {
            $image->withPresentation(Image::NON_INTERACTIVE);
        }

        if ($this->getProperty(self::PROPERTY_LIKE, $node)) {
            $image->enableLike();
        }

        if ($this->getProperty(self::PROPERTY_COMMENTS, $node)) {
            $image->enableComments();
        }

        $caption = null;
        if ($this->getProperty(self::PROPERTY_CAPTION, $node)) {
            $caption = Caption::create();
            $transformer->transform($caption, $this->getProperty(self::PROPERTY_CAPTION, $node));
        }
        if ($this->getProperty(self::PROPERTY_CREDIT, $node)) {
            if ($caption === null) {
                $caption = Caption::create();
            }
            $credit = Cite::create();
            $transformer->transform($credit, $this->getProperty(self::PROPERTY_CREDIT, $node));
            $caption->withCredit($credit);
        }
        if ($caption !== null) {
            $image->withCaption($caption);
        }

        $suppress_warnings = $transformer->suppress_warnings;
        $transformer->suppress_warnings = true;
        $transformer->transform($image, $node);
        $transformer->suppress_warnings = $suppress_warnings;

        return $context;
    }
}
