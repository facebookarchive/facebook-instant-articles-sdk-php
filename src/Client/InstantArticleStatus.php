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

class InstantArticleStatus
{
    const SUCCESS = 'success';
    const NOT_FOUND = 'not_found';
    const IN_PROGRESS = 'in_progress';
    const FAILED = 'failed';
    const UNKNOWN = 'unknown';

    /**
     * @var ServerMessage[]
     */
    private $messages = [];

    /**
     * Instantiates a new InstantArticleStatus object.
     *
     * @param string $status
     * @param ServerMessage[] $messages
     */
    public function __construct($status, $messages = [])
    {
        Type::enforceWithin(
            $status,
            [
                self::SUCCESS,
                self::NOT_FOUND,
                self::IN_PROGRESS,
                self::FAILED,
                self::UNKNOWN
            ]
        );
        Type::enforceArrayOf(
            $messages,
            ServerMessage::getClassName()
        );
        $this->status = $status;
        $this->messages = $messages;
    }

    /**
    * Creates a instance from a status string,.
    *
    * @param string $status the status string, case insensitive.
    * @param array $messages the message from the server
    *
    * @return InstantArticleStatus
    */
    public static function fromStatus($status, $messages)
    {
        $status = strtolower($status);
        $validStatus = Type::isWithin(
            $status,
            [
                self::SUCCESS,
                self::NOT_FOUND,
                self::IN_PROGRESS,
                self::FAILED
            ]
        );
        if ($validStatus) {
            return new self($status, $messages);
        } else {
            \Logger::getLogger('facebook-instantarticles-client')
                ->info("Unknown status '$status'. Are you using the last SDK version?");
            return new self(self::UNKNOWN, $messages);
        }
    }

    /**
     * @param ServerMessage[] $messages
     *
     * @return InstantArticleStatus
     */
    public static function success($messages = [])
    {
        return new self(self::SUCCESS, $messages);
    }

    /**
     * @param ServerMessage[] $messages
     *
     * @return InstantArticleStatus
     */
    public static function notFound($messages = [])
    {
        return new self(self::NOT_FOUND, $messages);
    }

    /**
     * @param ServerMessage[]  $messages
     *
     * @return InstantArticleStatus
     */
    public static function inProgress($messages = [])
    {
        return new self(self::IN_PROGRESS, $messages);
    }

    /**
     * @param ServerMessage[] $messages
     *
     * @return InstantArticleStatus
     */
    public static function failed($messages = [])
    {
        return new self(self::FAILED, $messages);
    }

    /**
     * @param ServerMessage[] $messages
     *
     * @return InstantArticleStatus
     */
    public static function unknown($messages = [])
    {
        return new self(self::UNKNOWN, $messages);
    }

    /**
     * @param ServerMessage $message
     */
    public function addMessage($message)
    {
        Type::enforce(
            $message,
            ServerMessage::getClassName()
        );
        $this->messages[] = $message;
    }

    /**
     * @return ServerMessage[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
