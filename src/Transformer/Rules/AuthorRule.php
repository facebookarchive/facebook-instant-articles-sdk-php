<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Author;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class AuthorRule extends ConfigurationSelectorRule
{
    const PROPERTY_AUTHOR_URL = 'author.url';
    const PROPERTY_AUTHOR_NAME = 'author.name';
    const PROPERTY_AUTHOR_ROLE_CONTRIBUTION = 'author.role_contribution';
    const PROPERTY_AUTHOR_DESCRIPTION = 'author.description';

    public function getContextClass()
    {
        return Header::getClassName();
    }

    public static function create()
    {
        return new AuthorRule();
    }

    public static function createFrom($configuration)
    {
        $author_rule = AuthorRule::create();

        $author_rule->withSelector($configuration['selector']);
        $properties = $configuration['properties'];
        $author_rule->withProperties(
            [
                self::PROPERTY_AUTHOR_URL,
                self::PROPERTY_AUTHOR_NAME,
                self::PROPERTY_AUTHOR_DESCRIPTION,
                self::PROPERTY_AUTHOR_ROLE_CONTRIBUTION
            ],
            $properties
        );

        return $author_rule;
    }

    public function apply($transformer, $header, $node)
    {
        $author = Author::create();

        // Builds the author

        $url = $this->getProperty(self::PROPERTY_AUTHOR_URL, $node);
        $name = $this->getProperty(self::PROPERTY_AUTHOR_NAME, $node);
        $role_contribution = $this->getProperty(self::PROPERTY_AUTHOR_ROLE_CONTRIBUTION, $node);
        $description = $this->getProperty(self::PROPERTY_AUTHOR_DESCRIPTION, $node);

        if ($name) {
            $author->withName($name);
            $header->addAuthor($author);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_AUTHOR_NAME,
                    $header,
                    $node,
                    $this
                )
            );
        }

        if ($role_contribution) {
            $author->withRoleContribution($role_contribution);
        }

        if ($description) {
            $author->withDescription($description);
        }

        if ($url) {
            $author->withURL($url);
        }

        return $header;
    }
}
