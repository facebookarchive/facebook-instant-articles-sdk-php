<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Warnings;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Validators\Type;

class DeprecatedRuleWarning
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var Element
     */
    private $context;

    /**
     * @var \DOMNode
     */
    private $node;

    /**
     * @var ConfigurationSelectorRule
     */
    private $rule;

    /**
     * @param string $message
     * @param Element $context
     * @param \DOMNode $node
     * @param ConfigurationSelectorRule $rule
     */
    public function __construct($message, $context, $node, $rule)
    {
        $this->message = $message;
        $this->context = $context;
        $this->node = $node;
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * @return Element
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return \DOMNode
     */
    public function getNode()
    {
        return $this->node;
    }
}
