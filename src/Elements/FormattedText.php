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
 * Base class for text formatting elements.
 * It has exactly the same behavior as TextContainer, but TextContainer cannot be nested,
 * as it only accepts FormattedText children. Because of that you can freely nest FormattedText.
 *
 * Examples:
 * - <b>bold</b>
 * - <i>italic</i>
 * - <a href="https://foo.com">link</a>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
abstract class FormattedText extends TextContainer
{
}
