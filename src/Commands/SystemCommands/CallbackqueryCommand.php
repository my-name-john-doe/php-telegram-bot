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
use Longman\TelegramBot\Conversation;

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
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_query = $this->getCallbackQuery();

        // Call all registered callbacks.
        $callbacks_ret = null;
        foreach (self::$callbacks as $callback) {
            $callbacks_ret = $callback($callback_query);
        }

        // If callback data is a command, execute it.
        if (preg_match('/^\/([^\s@]+)/', $callback_query->getData(), $command)) {
            $ret = $this->telegram->executeCommand($command[1]);
            if ($ret instanceOf AnswerCallbackQuery) return $this->answer($ret);
        }

        // Or, if there is an active conversation, execute the command that started it. 
        else if ($message = $callback_query->getMessage()) {
            $conversation = new Conversation(
                $callback_query->getFrom()->getId(),
                $message->getChat()->getId()
            );

            if ($conversation->exists() && ($command = $conversation->getCommand())) {
                $ret = $this->telegram->executeCommand($command);
                if ($ret instanceOf AnswerCallbackQuery) return $this->answer($ret);
            }
        }

        // Then, return last callback's answer
        if ($callbacks_ret instanceOf AnswerCallbackQuery) return $this->answer($callbacks_ret);

        // Finally, answer this thing if nothing above did.
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
