<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Entities\AnswerCallbackQuery;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Callback query command
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var callable[]
     */
    protected static $callbacks = [];

    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_query = $this->getCallbackQuery();

        // Call all registered callbacks, until one returns AnswerCallbackQuery.
        foreach (self::$callbacks as $callback) {
            $callbacks_ret = $callback($callback_query, $this->getTelegram());
            if ($callbacks_ret instanceOf AnswerCallbackQuery) return $this->answer($callbacks_ret);
        }

        return $this->answer(new AnswerCallbackQuery);
    }

    /**
     * Add a new callback handler for callback queries.
     *
     * @param $callback
     */
    public static function addCallbackHandler($callback)
    {
        self::$callbacks[] = $callback;
    }

    /**
     * Answer callback query
     *
     * @param \Longman\TelegramBot\Entities\AnswerCallbackQuery $params
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    protected function answer(AnswerCallbackQuery $params)
    {
        $params = $params->getAnswerCallbackQuery();
        $params['callback_query_id'] = $this->getCallbackQuery()->getId();
        return Request::answerCallbackQuery($params);
    }
}
