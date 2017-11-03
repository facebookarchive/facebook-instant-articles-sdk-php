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
     * @var InstantArticle the initial context.
     */
    private $instantArticle;

    /**
     * @var DateTimeZone the timezone for parsing dates. It defaults to 'America/Los_Angeles', but can be customized.
     */
    private $defaultDateTimeZone;

    /**
     * Flag attribute added to elements processed by a getter, so they
     * are not processed again by other rules.
     */
    const INSTANT_ARTICLES_PARSED_FLAG = 'data-instant-articles-element-processed';

    /**
     * Initializes default values.
     */
    public function __construct()
    {
        $this->defaultDateTimeZone = new \DateTimeZone('America/Los_Angeles');
    }

    /**
     * Clones a node for appending to raw-html containing Elements like Interactive.
     *
     * @param DOMNode $node The node to clone
     * @return DOMNode The cloned node.
     */
    public static function cloneNode($node)
    {
        $clone = $node->cloneNode(true);
        if (Type::is($clone, 'DOMElement') && $clone->hasAttribute(self::INSTANT_ARTICLES_PARSED_FLAG)) {
            $clone->removeAttribute(self::INSTANT_ARTICLES_PARSED_FLAG);
        }
        return $clone;
    }

    /**
     * Marks a node as processed.
     *
     * @param DOMElement $node The node to clone
     */
    public static function markAsProcessed($node)
    {
        if (Type::is($node, 'DOMElement')) {
            $node->setAttribute(self::INSTANT_ARTICLES_PARSED_FLAG, true);
        }
    }

    /**
     * Returns whether a node is processed
     *
     * @param DOMNode $node The node to clone
     */
    protected static function isProcessed($node)
    {
        return Type::is($node, 'DOMElement') && $node->getAttribute(self::INSTANT_ARTICLES_PARSED_FLAG);
    }


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
     * @return InstantArticle the initial context of this Transformer
     */
    public function getInstantArticle()
    {
        return $this->instantArticle;
    }

    /**
     * @param InstantArticle $context
     * @param string $content
     *
     * @return mixed
     */
    public function transformString($context, $content, $encoding = "utf-8")
    {
        $libxml_previous_state = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0');
        if (function_exists('mb_convert_encoding')) {
            $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', $encoding));
        } else {
            $log = \Logger::getLogger('facebook-instantarticles-transformer');
            $log->debug(
                'Your content encoding is "' . $encoding . '" ' .
                'but your PHP environment does not have mbstring. Trying to load your content with using meta tags.'
            );
            // wrap the content with charset meta tags
            $document->loadHTML(
                '<html><head>' .
                '<meta http-equiv="Content-Type" content="text/html; charset=' . $encoding . '">' .
                '</head><body>' . $content . '</body></html>'
            );
        }
        libxml_clear_errors();
        libxml_use_internal_errors($libxml_previous_state);
        return $this->transform($context, $document);
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
            $this->instantArticle = $context;
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
                if (self::isProcessed($child)) {
                    continue;
                }
                $matched = false;
                $log->debug("===========================");
                $log->debug($child->ownerDocument->saveHtml($child));

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
     * @param Rule[] $rules List of configured rules.
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

    /**
     * Sets the default timezone for parsing dates.
     *
     * @param DateTimeZone $dateTimeZone
     */
    public function setDefaultDateTimeZone($dateTimeZone)
    {
        Type::enforce($dateTimeZone, 'DateTimeZone');
        $this->defaultDateTimeZone = $dateTimeZone;
    }

    /**
     * Gets the default timezone for parsing dates.
     *
     * @return DateTimeZone
     */
    public function getDefaultDateTimeZone()
    {
        return $this->defaultDateTimeZone;
    }
}
