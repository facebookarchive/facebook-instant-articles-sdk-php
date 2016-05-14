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
 * Represents an author of the article.
 *
 * <address>
 *    <a rel="facebook" href="http://facebook.com/everton.rosario">Everton</a>
 *    Everton Rosario is a passionate mountain biker on Facebook
 * </address>
 *
 * or
 *
 * <address>
 *    <a href="http://twitter.com/evertonrosario">Everton On Twitter</a>
 *    Everton Rosario is a passionate mountain biker on Twitter
 * </address>
 *
 * or
 *
 * <address>
 *    <a>Everton</a>
 *    Everton Rosario is a passionate mountain biker without Link
 * </address>
 */
class Author extends Element
{
    /**
     * @var string The author link
     */
    private $url;

    /**
     * @var string The author name
     */
    private $name;

    /**
     * @var string The author short description biography
     */
    private $description;

    /**
     * @var string Role or contribution of author
     */
    private $roleContribution;

    private function __construct()
    {
    }

    /**
     * Creates an Author instance.
     *
     * @return Author
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Defines the link URL for the author
     *
     * @param string $url the URL link for author. Ex: "http://facebook.com/everton.rosario"
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
     * Author name.
     *
     * @param string $name Author name. Ex: "Everton Rosario"
     *
     * @return $this
     */
    public function withName($name)
    {
        Type::enforce($name, Type::STRING);
        $this->name = $name;

        return $this;
    }

    /**
     * Author short description biography
     *
     * @param string $description Describe the author biography.
     *
     * @return $this
     */
    public function withDescription($description)
    {
        Type::enforce($description, Type::STRING);
        $this->description = $description;

        return $this;
    }

    /**
     * Author role/contribution
     *
     * @param string $role_contribution The author short text to characterize role or contribution
     *
     * @return $this
     */
    public function withRoleContribution($role_contribution)
    {
        Type::enforce($role_contribution, Type::STRING);
        $this->roleContribution = $role_contribution;

        return $this;
    }

    /**
     * @return string author link url profile
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string author name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string author small introduction biography
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string author short text to define its contribution/role
     */
    public function getRoleContribution()
    {
        return $this->roleContribution;
    }

    /**
     * Structure and create the full Author in a XML format DOMElement.
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

        $author_url = $this->url ? $this->url : null;
        $is_fb_author = strpos($author_url, 'facebook.com') !== false;

        // Creates the root tag <address></address>
        $element = $document->createElement('address');

        // Creates the <a href...></> tag
        $ahref = $document->createElement('a');
        if ($author_url) {
            $ahref->setAttribute('href', $author_url);
        }
        if ($is_fb_author) {
            $ahref->setAttribute('rel', 'facebook');
        }
        if ($this->roleContribution) {
            $ahref->setAttribute('title', $this->roleContribution);
        }
        $ahref->appendChild($document->createTextNode($this->name));
        $element->appendChild($ahref);

        // Appends author description
        $element->appendChild($document->createTextNode($this->description));

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Author that contains not empty name, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->name);
    }
}
