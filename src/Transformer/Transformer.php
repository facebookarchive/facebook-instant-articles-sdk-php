<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer;

use Facebook\InstantArticles\Transformer\Warnings\UnrecognizedElement;
use Facebook\InstantArticles\Transformer\Rules\Rule;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Validators\InstantArticleValidator;

class Transformer
{
    /**
     * @var Rule[]
     */
    private $rules = [];

    /**
     * @var array
     */
    private $warnings = [];

    /**
     * @var int
     */
    private $ruleCount = 0;

    /**
     * @var bool
     */
    public $suppress_warnings = false;

    /**
     * @var array
     */
    private static $allClassTypes = [];

    /**
     * Gets all types a given class is, including itself, parent classes and interfaces.
     *
     * @param string $className - the name of the className
     *
     * @return array of class names the provided class name is
     */
    private static function getAllClassTypes($className)
    {
        // Memoizes
        if (isset(self::$allClassTypes[$className])) {
            return self::$allClassTypes[$className];
        }

        $classParents = class_parents($className, true);
        $classInterfaces = class_implements($className, true);
        $classNames = [$className];
        if ($classParents) {
            $classNames = array_merge($classNames, $classParents);
        }
        if ($classInterfaces) {
            $classNames = array_merge($classNames, $classInterfaces);
        }
        self::$allClassTypes[$className] = $classNames;
        return $classNames;
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param Rule $rule
     */
    public function addRule($rule)
    {
        Type::enforce($rule, Rule::getClassName());

        // Use context class as a key
        $contexts = $rule->getContextClass();

        // Handles multiple contexts
        if (!is_array($contexts)) {
            $contexts = [$contexts];
        }

        foreach ($contexts as $context) {
            if (!isset($this->rules[$context])) {
                $this->rules[$context] = [];
            }
            $this->rules[$context][$this->ruleCount++] = $rule;
        }
    }

    /**
     * @param $warning
     */
    public function addWarning($warning)
    {
        $this->warnings[] = $warning;
    }

    /**
     * @param InstantArticle $context
     * @param \DOMNode $node
     *
     * @return mixed
     */
    public function transform($context, $node)
    {
        if (Type::is($context, InstantArticle::getClassName())) {
            $context->addMetaProperty('op:generator:transformer', 'facebook-instant-articles-sdk-php');
            $context->addMetaProperty('op:generator:transformer:version', InstantArticle::CURRENT_VERSION);
        }

        $log = \Logger::getLogger('facebook-instantarticles-transformer');
        if (!$node) {
            $e = new \Exception();
            $log->error(
                'Transformer::transform($context, $node) requires $node'.
                ' to be a valid one. Check on the stacktrace if this is '.
                'some nested transform operation and fix the selector.',
                $e->getTraceAsString()
            );
        }
        $current_context = $context;
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                $matched = false;
                $log->debug("===========================");

                // Get all classes and interfaces this context extends/implements
                $contextClassNames = self::getAllClassTypes($context->getClassName());

                // Look for rules applying to any of them as context
                $matchingContextRules = [];
                foreach ($contextClassNames as $contextClassName) {
                    if (isset($this->rules[$contextClassName])) {
                        // Use array union (+) instead of merge to preserve
                        // indexes (as they represent the order of insertion)
                        $matchingContextRules = $matchingContextRules + $this->rules[$contextClassName];
                    }
                }

                // Sort by insertion order
                ksort($matchingContextRules);

                // Process in reverse order
                $matchingContextRules = array_reverse($matchingContextRules);
                foreach ($matchingContextRules as $rule) {
                    // We know context was matched, now check if it matches the node
                    if ($rule->matchesNode($child)) {
                        $current_context = $rule->apply($this, $current_context, $child);
                        $matched = true;

                        // Just a single rule for each node, so move on
                        break;
                    }
                }

                if (!$matched &&
                    !($child->nodeName === '#text' && trim($child->textContent) === '') &&
                    !($child->nodeName === '#comment') &&
                    !($child->nodeName === 'html' && Type::is($child, 'DOMDocumentType')) &&
                    !($child->nodeName === 'xml' && Type::is($child, 'DOMProcessingInstruction')) &&
                    !$this->suppress_warnings
                    ) {
                    $tag_content = $child->ownerDocument->saveXML($child);
                    $tag_trimmed = trim($tag_content);
                    if (!empty($tag_trimmed)) {
                        $log->debug('context class: '.get_class($context));
                        $log->debug('node name: '.$child->nodeName);
                        $log->debug("CONTENT NOT MATCHED: \n".$tag_content);
                    } else {
                        $log->debug('empty content ignored');
                    }

                    $this->addWarning(new UnrecognizedElement($current_context, $child));
                }
            }
        }

        if (Type::is($context, InstantArticle::getClassName())) {
            $ia_warnings = InstantArticleValidator::check($context);
            foreach ($ia_warnings as $ia_warning) {
                $this->addWarning($ia_warning);
            }
        }

        return $context;
    }

    /**
     * @param string $json_file
     */
    public function loadRules($json_file)
    {
        $configuration = json_decode($json_file, true);
        if ($configuration && isset($configuration['rules'])) {
            foreach ($configuration['rules'] as $configuration_rule) {
                $class = $configuration_rule['class'];
                try {
                    $factory_method = new \ReflectionMethod($class, 'createFrom');
                } catch (\ReflectionException $e) {
                    $factory_method =
                        new \ReflectionMethod(
                            'Facebook\\InstantArticles\\Transformer\\Rules\\'.$class,
                            'createFrom'
                        );
                }
                $this->addRule($factory_method->invoke(null, $configuration_rule));
            }
        }
    }

    /**
     * Removes all rules already set in this transformer instance.
     */
    public function resetRules()
    {
        $this->rules = [];
        $this->ruleCount = 0;
    }

    /**
     * Gets all rules already set in this transformer instance.
     *
     * @return Rule[] List of configured rules.
     */
    public function getRules()
    {
        // Do not expose internal map, just a simple array
        // to keep the interface backwards compatible.
        $flatten_rules = [];
        foreach ($this->rules as $ruleset) {
            foreach ($ruleset as $priority => $rule) {
                $flatten_rules[$priority] = $rule;
            }
        }

        ksort($flatten_rules);
        return $flatten_rules;
    }

    /**
     * Overrides all rules already set in this transformer instance.
     *
     * @return Rule[] List of configured rules.
     */
    public function setRules($rules)
    {
        // Do not receive internal map, just a plain list
        // to keep the interface backwards compatible.
        Type::enforceArrayOf($rules, Rule::getClassName());
        $this->resetRules();
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }
}
