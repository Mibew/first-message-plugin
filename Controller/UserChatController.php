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

namespace Mibew\Mibew\Plugin\FirstMessage\Controller;

use Mibew\Controller\Chat\UserChatController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Mibew\Mibew\Plugin\FirstMessage\Message;

/**
 * A controller which handles user's chat start actions.
 *
 * It's just a decorator for {@link \Mibew\Controller\Chat\UserChatController}.
 */
class UserChatController extends BaseController
{
    /**
     * Saves custom user's message and starts chat for him.
     *
     * @param Request $request Incoming request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function startAction(Request $request)
    {
        // Check if there is a message that should be posted to the thread
        $first_message = $request->query->get('first_message');

        if ($first_message) {
            $visitor_data = visitor_from_request();
            $user_id = $visitor_data['id'];

            $message = Message::loadByUserId($user_id);
            if (!$message) {
                $message = new Message($user_id);
            }

            $message->setMessage($first_message);
            $message->save();
        }

        return parent::startAction($request);
    }
}
