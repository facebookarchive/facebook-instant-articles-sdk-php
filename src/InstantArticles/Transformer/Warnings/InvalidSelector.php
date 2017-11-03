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
use Facebook\InstantArticles\Transformer\Rules\ConfigurationSelectorRule;

/**
 * Class InvalidSelector warning to show that an invalid selector for a required property was used
 */
class InvalidSelector
{
    /**
     * @var string
     */
    private $fields;

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
     * @param string $fields
     * @param Element $context
     * @param \DOMNode $node
     * @param ConfigurationSelectorRule $rule
     */
    public function __construct($fields, $context, $node, $rule)
    {
        $this->fields = $fields;
        $this->context = $context;
        $this->node = $node;
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (isset($this->context)) {
            $reflection = new \ReflectionClass(get_class($this->context));
            $class_name = $reflection->getShortName();
        } else {
            $class_name = 'no context provided';
        }

        if (isset($this->rule)) {
            $reflection = new \ReflectionClass(get_class($this->rule));
            $rule_name = $reflection->getShortName();
        } else {
            $rule_name = 'no rule provided';
        }

        $has_properties = false;
        $str_properties = '';
        foreach ($this->rule->getProperties() as $name => $value) {
            if (!$has_properties) {
                $str_properties = '';
            } else {
                $str_properties = $str_properties.',';
            }

            $reflection = new \ReflectionClass(get_class($value));
            $value_name = $reflection->getShortName();
            $str_properties = $str_properties.' '.$name.'='.$value_name;
        }
        return "Invalid selector for fields ({$this->fields}). ".
            "The node being transformed was <{$this->node->nodeName}> in the ".
            "context of $class_name within the Rule $rule_name with these ".
            "properties: {{$str_properties}}";
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
