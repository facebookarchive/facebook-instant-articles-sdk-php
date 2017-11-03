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
 * Class Map
 * This element Class holds map content for the articles.
 *
 * Example:
 *  <figure class="op-map">
 *    <script type="application/json" class="op-geotag">
 *      {
 *          "type": "Feature",
 *          "geometry": {
 *               "type": "Point",
 *               "coordinates": [23.166667, 89.216667]
 *          },
 *          "properties": {
 *               "title": "Jessore, Bangladesh",
 *               "radius": 750000,
 *               "pivot": true,
 *               "style": "satellite",
 *           }
 *       }
 *    </script>
 *  </figure>
 *
 */
class GeoTag extends Element
{
    /**
     * @var string The json geotag content inside the script geotag
     */
    private $script;

    private function __construct()
    {
    }

    /**
     * @return GeoTag
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the geotag on the image.
     *
     * @see {link:http://geojson.org/}
     *
     * @param string $script
     *
     * @return $this
     */
    public function withScript($script)
    {
        Type::enforce($script, Type::STRING);
        $this->script = $script; // TODO Validate the json informed

        return $this;
    }

    /**
     * @return string Geotag json content unescaped
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Structure and create the full Map in a XML format DOMElement.
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

        $element = $document->createElement('script');
        $element->setAttribute('type', 'application/json');
        $element->setAttribute('class', 'op-geotag');

        // Required script field
        if ($this->script) {
            // script may contain html entities so import it as CDATA
            $element->appendChild(
                $element->ownerDocument->importNode(new \DOMCdataSection($this->script), true)
            );
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid GeoTag that contains not empty script, false otherwise.
     */
    public function isValid()
    {
        return !Type::isTextEmpty($this->script);
    }
}
