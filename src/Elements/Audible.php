<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

/**
 * Abstract class Audible
 * This class represents all elements that can contain Audio
 * <ul>
 *     <li>Image</li>
 *     <li>SlideShow</li>
 * </ul>.
 *
 * Example:
 *  <audio>
 *      <source src="http://mydomain.com/path/to/audio.mp3" />
 *  </audio>
 *
 * @see Image
 * @see SlideShow
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/image}
 */
abstract class Audible extends Element implements Container
{
    /**
     * Adds audio to this image.
     *
     * @param Audio $audio The audio object
     *
     * @return $this
     */
    abstract public function withAudio($audio);
}
