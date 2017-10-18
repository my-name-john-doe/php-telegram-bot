<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Entities;

/**
 * Class AnswerCallbackQuery
 *
 * @link https://core.telegram.org/bots/api#answercallbackquery
 *
 * @method array getAnswerCallbackQuery() Parameters for \Longman\TelegramBot\Request::answerCallbackQuery()
 */
class AnswerCallbackQuery extends ServerResponse
{
    /**
     * AnswerCallbackQuery constructor.
     *
     * @param array  $data
     * @param string $bot_username
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function __construct(array $data = [], $bot_username = '')
    {
        $data = [
            'ok' => true,
            'answer_callback_query' => $data
        ];
        parent::__construct($data, $bot_username);
    }
}
