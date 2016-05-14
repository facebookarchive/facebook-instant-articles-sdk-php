<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Client;

use Facebook\InstantArticles\Validators\Type;

class ServerMessage
{
    const FATAL = 'fatal';
    const ERROR = 'error';
    const WARNING = 'warning';
    const INFO = 'info';

    /**
     * @var string
     */
    private $level;

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $level
     * @param string $message
     */
    public function __construct($level, $message)
    {
        Type::enforceWithin(
            $level,
            [
                self::FATAL,
                self::ERROR,
                self::WARNING,
                self::INFO
            ]
        );
        Type::enforce(
            $message,
            Type::STRING
        );
        $this->level = $level;
        $this->message = $message;
    }

    /**
    * Creates a message from a level string, using INFO if a invalid level string is provided.
    *
    * @param string $level the level string, case insensitive.
    * @param string $message the message from the server
    *
    * @return ServerMessage the message with the proper level
    */
    public static function fromLevel($level, $message)
    {
        $level = strtolower($level);
        $validLevel = Type::isWithin(
            $level,
            [
                self::FATAL,
                self::ERROR,
                self::WARNING,
                self::INFO
            ]
        );
        if ($validLevel) {
            return new self($level, $message);
        } else {
            \Logger::getLogger('facebook-instantarticles-client')
                ->info('Unknown message level "$level". Are you using the last SDK version?');
            return new self(self::INFO, $message);
        }
    }

    /**
     * @param string $message
     *
     * @return ServerMessage
     */
    public static function fatal($message)
    {
        return new self(self::FATAL, $message);
    }

    /**
     * @param string $message
     *
     * @return ServerMessage
     */
    public static function error($message)
    {
        return new self(self::ERROR, $message);
    }

    /**
     * @param string $message
     *
     * @return ServerMessage
     */
    public static function warning($message)
    {
        return new self(self::WARNING, $message);
    }

    /**
     * @param string $message
     *
     * @return ServerMessage
     */
    public static function info($message)
    {
        return new self(self::INFO, $message);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Auxiliary method to extract full qualified class name.
     * @return string The full qualified name of class
     */
    public static function getClassName()
    {
        return get_called_class();
    }
}
