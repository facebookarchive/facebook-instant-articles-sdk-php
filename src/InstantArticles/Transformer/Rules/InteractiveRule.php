<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Interactive;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Validators\Type;

class InteractiveRule extends ConfigurationSelectorRule
{
    const PROPERTY_IFRAME = 'interactive.iframe';
    const PROPERTY_URL = 'interactive.url';
    const PROPERTY_WIDTH_NO_MARGIN = Interactive::NO_MARGIN;
    const PROPERTY_WIDTH_COLUMN_WIDTH = Interactive::COLUMN_WIDTH;
    const PROPERTY_HEIGHT = 'interactive.height';
    const PROPERTY_WIDTH = 'interactive.width';

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
        $interactive_rule = static::create();
        $interactive_rule->withSelector($configuration['selector']);

        $interactive_rule->withProperties(
            [
                self::PROPERTY_IFRAME,
                self::PROPERTY_URL,
                self::PROPERTY_WIDTH_NO_MARGIN,
                self::PROPERTY_WIDTH_COLUMN_WIDTH,
                self::PROPERTY_WIDTH,
                self::PROPERTY_HEIGHT
            ],
            $configuration
        );

        return $interactive_rule;
    }

    public function apply($transformer, $context, $node)
    {
        $interactive = Interactive::create();

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

        // Builds the interactive
        $iframe = $this->getProperty(self::PROPERTY_IFRAME, $node);
        $url = $this->getProperty(self::PROPERTY_URL, $node);
        if ($iframe) {
            $interactive->withHTML($iframe);
        }
        if ($url) {
            $interactive->withSource($url);
        }
        if ($iframe || $url) {
            $instant_article->addChild($interactive);
            if ($instant_article !== $context) {
                $instant_article->addChild($context);
            }
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IFRAME,
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        if ($this->getProperty(self::PROPERTY_WIDTH_COLUMN_WIDTH, $node)) {
            $interactive->withMargin(Interactive::COLUMN_WIDTH);
        } else {
            $interactive->withMargin(Interactive::NO_MARGIN);
        }

        $width = $this->getProperty(self::PROPERTY_WIDTH, $node);
        if ($width) {
            $interactive->withWidth($width);
        }

        $height = $this->getProperty(self::PROPERTY_HEIGHT, $node);
        if ($height) {
            $interactive->withHeight($height);
        }

        $suppress_warnings = $transformer->suppress_warnings;
        $transformer->suppress_warnings = true;
        $transformer->transform($interactive, $node);
        $transformer->suppress_warnings = $suppress_warnings;

        return $context;
    }
}
