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

namespace Mibew\Mibew\Plugin\FirstMessage;

use Mibew\Database;

/**
 * Represents a message which is sent be an user.
 */
class Message
{
    /**
     * Internal unique ID of the message.
     *
     * @var int|bool
     */
    protected $id = false;

    /**
     * ID of the user who sent the message.
     *
     * @var string
     */
    protected $userId = false;

    /**
     * The message sent by user.
     *
     * @var string
     */
    protected $message = '';

    /**
     * Class constructor.
     *
     * @param string $user_id ID of the user who sent the message.
     *
     * @throws \InvalidArgumentException If the ID is invalid.
     */
    public function __construct($user_id)
    {
        if (!$user_id) {
            throw new \InvalidArgumentException('User ID cannot be empty');
        }

        $this->userId = $user_id;
    }

    /**
     * Retrieves ID of the user who sent the message.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Retrieves message's body.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets message's body.
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Saves the message.
     *
     * @return bool True if the message is saved successfully and false on
     * error.
     */
    public function save()
    {
        $db = Database::getInstance();

        if ($this->id === false) {
            return $db->query(
                ('INSERT INTO {mibew_firstmessage} (userid, message) '
                    . 'VALUES (:user_id, :message)'),
                array(
                    ':user_id' => $this->getUserId(),
                    ':message' => $this->getMessage(),
                )
            );
        } else {
            return $db->query(
                ('UPDATE {mibew_firstmessage} SET message = :message '
                    . 'WHERE userid = :user_id'),
                array(
                    ':user_id' => $this->getUserId(),
                    ':message' => $this->getMessage(),
                )
            );
        }
    }

    /**
     * Removes previously saved message.
     */
    public function delete()
    {
        Database::getInstance()->query(
            'DELETE FROM {mibew_firstmessage} WHERE userid = :user_id',
            array(':user_id' => $this->getUserId())
        );
    }

    /**
     * Loads message object by it's ID.
     *
     * @param int $id Internal ID of the message.
     * @return Message|boolean An instance of
     * {@link \Mibew\Mibew\Plugin\FirstMessage\Message} or boolean false if
     * there is no message with specified ID.
     *
     * @throws \InvalidArgumentException If the ID is invalid.
     */
    public static function load($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Message ID cannot be empty');
        }

        $info = Database::getInstance()->query(
            'SELECT * FROM {mibew_firstmessage} WHERE id = :id',
            array(':id' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        if (!$info) {
            return false;
        }

        return self::buildFromDbFields($info);
    }

    /**
     * Loads message by user ID.
     *
     * @param string $user_id ID of the user who sent message.
     * @return Message|boolean An instance of
     * {@link \Mibew\Mibew\Plugin\FirstMessage\Message} or boolean false if
     * there is no message related with specified user ID.
     *
     * @throws \InvalidArgumentException If the user ID is invalid.
     */
    public static function loadByUserId($user_id)
    {
        if (!$user_id) {
            throw new \InvalidArgumentException('User ID cannot be empty');
        }

        $info = Database::getInstance()->query(
            'SELECT * FROM {mibew_firstmessage} WHERE userid = :user_id',
            array(':user_id' => $user_id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        if (!$info) {
            return false;
        }

        return self::buildFromDbFields($info);
    }

    /**
     * Builds an instance of Message based on database fields.
     *
     * @param array $fields List of database fields related with the message.
     *
     * @return Message
     */
    protected static function buildFromDbFields($fields)
    {
        $message = new self($fields['userid']);

        $message->id = $fields['id'];
        $message->setMessage($fields['message']);

        return $message;
    }
}
