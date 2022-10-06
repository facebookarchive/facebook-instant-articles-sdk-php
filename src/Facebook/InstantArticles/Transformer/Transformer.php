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
use Facebook\InstantArticles\Transformer\Logs\TransformerLog;
use Facebook\InstantArticles\Transformer\Rules\Rule;
use Facebook\InstantArticles\Transformer\Settings\AdSettings;
use Facebook\InstantArticles\Transformer\Settings\AnalyticsSettings;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Ad;
use Facebook\InstantArticles\Elements\Analytics;
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
     * @var string The default style name to be used on the Instant Article generated from this transformation.
     */
    private $defaultStyleName;

    /**
     * @var AdSettings The content settings for ads on this transformation cycle.
     */
    private $adsSettings;

    /**
     * @var AnalyticsSettings The content settings for analytics on this transformation cycle.
     */
    private $analyticsSettings;

    /**
     * Flag attribute added to elements processed by a getter, so they
     * are not processed again by other rules.
     */
    const INSTANT_ARTICLES_PARSED_FLAG = 'data-instant-articles-element-processed';

    /**
     * @var TransformerLog[] $logs The log messages generated during the transformation process.
     */
    private $logs = array();

    /**
     * Initializes default values.
     */
    public function __construct()
    {
        $this->defaultDateTimeZone = new \DateTimeZone('America/Los_Angeles');
        $this->addLog(
            TransformerLog::INFO,
            'Possible log levels: OFF, ERROR, INFO or DEBUG. To change it call method TransformerLog::setLevel("DEBUG").'
        );
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
     * @param $level string The log level message to be added. It will ignore if the level used at @see self::setLogLevel is not proper for this level message.
     * @param $logMessage string the Log message that will be added if the $level informed is proper based on @see self::setLogLevel.
     */
    private function addLog($level, $logMessage)
    {
        if (TransformerLog::isLevelEnabled($level)) {
            $this->logs[] = new TransformerLog($level, $logMessage);
        }
    }

    /**
     * Get the log information during the transformation. This should be called once, after transformation is finished already.
     * @return TransformerLog[] With each message being one item on this array.
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param InstantArticle $context
     * @param string $content
     *
     * @return mixed
     */
    public function transformString($context, $content, $encoding = "utf-8")
    {
        $start = microtime(true);
        $this->addLog(
            TransformerLog::INFO,
            "Transformer initiated using encode [$encoding]"
        );
        $this->addLog(
            TransformerLog::DEBUG,
            "Will transform content [$content]"
        );
        $libxml_previous_state = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0');
        if (function_exists('mb_convert_encoding')) {
            $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', $encoding));
        } else {
            $this->addLog(
                TransformerLog::DEBUG,
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
        $result = $this->transform($context, $document);
        $totalTime = round(microtime(true) - $start, 3)*1000;
        $totalWarnings = count($this->getWarnings());
        $this->addLog(
            TransformerLog::INFO,
            "Transformer finished in $totalTime ms with ($totalWarnings) warnings"
        );
        return $result;
    }

    /**
     * @param InstantArticle $context
     * @param \DOMNode $node
     *
     * @return mixed
     */
    public function transform($context, $node)
    {
        $is_first_run = false;
        if (Type::is($context, InstantArticle::getClassName()) && $context->getMetaProperty('op:generator:transformer') === null) {
            $context->addMetaProperty('op:generator:transformer', 'facebook-instant-articles-sdk-php');
            $context->addMetaProperty('op:generator:transformer:version', InstantArticle::CURRENT_VERSION);
            $is_first_run = true;
            $this->instantArticle = $context;
        }

        if (!$node) {
            $e = new \Exception();
            $this->addLog(
                TransformerLog::ERROR,
                'Transformer::transform($context, $node) requires $node'.
                ' to be a valid one. Check on the stacktrace if this is '.
                'some nested transform operation and fix the selector.',
                $e->getTraceAsString()
            );
            return $context;
        }
        $current_context = $context;
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if (self::isProcessed($child)) {
                    continue;
                }
                $matched = false;

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
                    $className = $rule->getClassName();
                    if ($rule->matchesNode($child)) {
                        $this->addLog(
                            TransformerLog::DEBUG,
                            "MATCH -> Rule [$className] applied to node [$child->nodeName]"
                        );
                        $current_context = $rule->apply($this, $current_context, $child);
                        $matched = true;

                        // Just a single rule for each node, so move on
                        break;
                    }

                    $this->addLog(
                        TransformerLog::DEBUG,
                        "no match -> rule [$className] not matched to node [$child->nodeName]"
                    );
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
                        $className = $context->getClassName();
                        $this->addLog(
                            TransformerLog::ERROR,
                            "Content with no rules matching! Context[$className] and Node [$child->nodeName]"
                        );
                    } else {
                        $this->addLog(
                            TransformerLog::DEBUG,
                            "Empty content ignored."
                        );
                    }

                    $this->addWarning(new UnrecognizedElement($current_context, $child));
                }
            }
        }

        if ($is_first_run) {
            $context = $this->handleTransformationSettings($context);
        }

        return $context;
    }

    /**
     * @param string $json_file
     *
     * @return configuration
     */
    public function validateJSON($json_file)
    {
        $configuration = json_decode($json_file, true);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                break;
            case JSON_ERROR_DEPTH:
                $this->addWarning('Invalid JSON Rules: Maximum stack depth exceeded');
                $this->addLog(
                    TransformerLog::ERROR,
                    "Invalid JSON Rules: Maximum stack depth exceeded"
                );
                $configuration = '';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $this->addWarning('Invalid JSON Rules: Underflow or the modes mismatch');
                $this->addLog(
                    TransformerLog::ERROR,
                    "Invalid JSON Rules: Underflow or the modes mismatch"
                );
                $configuration = '';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $this->addWarning('Invalid JSON Rules: Unexpected control character found');
                $this->addLog(
                    TransformerLog::ERROR,
                    "Invalid JSON Rules: Unexpected control character found"
                );
                $configuration = '';
                break;
            case JSON_ERROR_SYNTAX:
                $this->addWarning('Invalid JSON Rules: Syntax error, malformed JSON');
                $this->addLog(
                    TransformerLog::ERROR,
                    "Invalid JSON Rules: Syntax error, malformed JSON"
                );
                $configuration = '';
                break;
            case JSON_ERROR_UTF8:
                $this->addWarning('Invalid JSON Rules: Malformed UTF-8 characters, possibly incorrectly encoded');
                $this->addLog(
                    TransformerLog::ERROR,
                    "Invalid JSON Rules: Malformed UTF-8 characters, possibly incorrectly encoded"
                );
                $configuration = '';
                break;
            default:
                $this->addWarning('Invalid JSON Rules');
                $this->addLog(
                    TransformerLog::ERROR,
                    "Invalid JSON Rules"
                );
                $configuration = '';
                break;
        }
        return $configuration;
    }

    /**
     * @param string $json_file
     */
    public function loadRules($json_file)
    {
        $configuration = $this->validateJSON($json_file);

        // Treats the Rules configuration
        if ($configuration && isset($configuration['rules'])) {
            foreach ($configuration['rules'] as $configuration_rule) {
                $class = $configuration_rule['class'];
                try {
                    $factory_method = new \ReflectionMethod($class, 'createFrom');
                    $this->addRule($factory_method->invoke(null, $configuration_rule));
                } catch (\ReflectionException $e) {
                    try {
                        $factory_method = new \ReflectionMethod(
                            'Facebook\\InstantArticles\\Transformer\\Rules\\' . $class,
                            'createFrom'
                        );
                        $this->addRule($factory_method->invoke(null, $configuration_rule));
                    } catch (\ReflectionException $e) {
                        $this->addWarning("$class was not found");
                        $this->addLog(
                            TransformerLog::ERROR,
                            "$class was not found"
                        );
                    }
                }
            }
        }

        // Treats the ADS configuration
        if ($configuration && isset($configuration['ads'])) {
            $this->loadAdsConfiguration($configuration['ads']);
        }

        // Treats the Analyticds configuration
        if ($configuration && isset($configuration['analytics'])) {
            $this->loadAnalyicsConfiguration($configuration['analytics']);
        }

        // Treats the Style configuration
        if ($configuration && isset($configuration['style_name'])) {
            $this->setDefaultStyleName($configuration['style_name']);
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

    /**
     * Sets the default style to be applyied to the articles generated from this transformation.
     *
     * @param string $defaultStyleName
     */
    public function setDefaultStyleName($defaultStyleName)
    {
        Type::enforce($defaultStyleName, Type::STRING);
        $this->defaultStyleName = $defaultStyleName;
    }

    /**
     * Gets the default style name for Instant Article generated during transformation.
     *
     * @return string
     */
    public function getDefaultStyleName()
    {
        return $this->defaultStyleName;
    }

    /**
     * Applies the settings loaded from the rules.json informed on loadRules method.
     *
     * @param InstantArticle The InstantArticle to have the settings set.
     * @return InstantArticle The article with settings added if needed.
     */
    public function handleTransformationSettings($instantArticle)
    {
        if (!Type::isTextEmpty($this->getDefaultStyleName())) {
            $instantArticle->withStyle($this->getDefaultStyleName());
        }

        if ($this->adsSettings) {
            $ad = $this->adsSettings->getAdElement();
            if ($ad) {
                $instantArticle->getHeader()->addAd($ad);
            }
        }

        if ($this->analyticsSettings) {
            $analytics = $this->analyticsSettings->getAnalyticsElement();
            if ($analytics) {
                $instantArticle->addChild($analytics);
            }
        }

        return $instantArticle;
    }

    public function loadAdsConfiguration($adsSettings)
    {
        $this->adsSettings = new AdSettings(
            isset($adsSettings['audience_network_placement_id']) ? $adsSettings['audience_network_placement_id'] : '',
            isset($adsSettings['raw_html']) ? $adsSettings['raw_html'] : ''
        );
    }

    public function loadAnalyicsConfiguration($analyticsSettings)
    {
        $this->analyticsSettings = new AnalyticsSettings(
            isset($analyticsSettings['fb_pixel_id']) ? $analyticsSettings['fb_pixel_id'] : '',
            isset($analyticsSettings['raw_html']) ? $analyticsSettings['raw_html'] : ''
        );
    }
}
