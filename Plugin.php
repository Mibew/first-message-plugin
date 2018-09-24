<?php
/*
 * This file is a part of Mibew First Message Plugin.
 *
 * Copyright 2015 Dmitriy Simushev <simushevds@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @file The main file of Mibew:FirstMessage plugin.
 */

namespace Mibew\Mibew\Plugin\FirstMessage;

use Mibew\Database;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Thread;

/**
 * Represents the plugin.
 */
class Plugin extends \Mibew\Plugin\AbstractPlugin implements \Mibew\Plugin\PluginInterface
{
    /**
     * Class constructor.
     *
     * @param array $config List of the plugin config. The following options are
     * supported:
     *   - "template": string, can be used to customize user's message.
     *     To show user's message "{message}" placeholder can be used inside of
     *     the template. The default value is "{message}".
     */
    public function __construct($config)
    {
        parent::__construct($config + array(
            'template' => '{message}',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function initialized()
    {
        // Extra configs are not required so the plugin is always ready to work.
        return true;
    }

    /**
     * Registers event handlers.
     */
    public function run()
    {
        // Attach CSS and JS files of the plugin to chat window.
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->attachListener(Events::THREAD_USER_IS_READY, $this, 'sendFirstMessage');
        $dispatcher->attachListener(Events::THREAD_CLOSE, $this, 'removeOldMessage');
    }

    /**
     * Sends first user's message to the chat.
     *
     * It's a listener of
     * {@link \Mibew\EventDispatcher\Events::THREAD_USER_IS_READY} event.
     *
     * @param array $args List of arguments that are passed to the event.
     */
    public function sendFirstMessage(&$args)
    {
        $thread = $args['thread'];
        if ($thread->userId) {
            $message = Message::loadByUserId($thread->userId);
            if ($message) {
                $prepared_message = str_replace(
                    '{message}',
                    $message->getMessage(),
                    $this->config['template']
                );

                $thread->postMessage(
                    Thread::KIND_USER,
                    $prepared_message,
                    array('name' => $thread->userName)
                );

                // The message is not needed anymore.
                $message->delete();
            }
        }
    }

    /**
     * Removes messages for threads that are closed.
     *
     * It's a listener of {@link \Mibew\EventDispatcher\Events::THREAD_CLOSE}
     * event.
     *
     * @param array $args List of arguments that is passed to the event.
     */
    public function removeOldMessage(&$args)
    {
        $thread = $args['thread'];
        if ($thread->userId) {
            $message = Message::loadByUserId($thread->userId);
            if ($message) {
                // There is a first message associated with the user ID. Delete
                // it because the thread is closed now and the message cannot be
                // used anymore.
                $message->delete();
            }
        }
    }

    /**
     * Specify version of the plugin.
     *
     * @return string Plugin's version.
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * Specifies system requirements of the plugin.
     *
     * @return array List of requirements.
     */
    public static function getSystemRequirements()
    {
        return array(
            'mibew' => '>=2.1.0'
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function install()
    {
        return Database::getInstance()->query(
            'CREATE TABLE {mibew_firstmessage} ( '
                . 'id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, '
                . 'userid VARCHAR(255), '
                . 'message TEXT, '
                . 'UNIQUE KEY userid (userid) '
            . ') charset utf8 ENGINE=InnoDb'
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function uninstall()
    {
        return Database::getInstance()->query('DROP TABLE {mibew_firstmessage}');
    }
}
