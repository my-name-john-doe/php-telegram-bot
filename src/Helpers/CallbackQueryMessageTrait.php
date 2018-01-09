<?php
namespace Longman\TelegramBot\Helpers;
use Longman\TelegramBot\Commands\Command;

trait CallbackQueryMessageTrait implements CallbackQueryHandlerInterface
{
    public function executeCallbackQuery()
    {
        if (!($this instanceOf Command)) {
            // not a command
            return null;
        }

        $query = $this->getCallbackQuery();
        if (!$query) {
            // not a callback query
            return null;
        }

        // some update mangling

        $update = [
            'update_id' => 0,
            'callback_query' => $query->getRawData(),
            'message' => [
                'message_id' => 0,
                'from' => $query->getFrom()->getRawData(),
                'date' => time(),
                'text' => $query->getData()
            ]
        ];

        if ($query->getMessage()) {
            $update['message']['chat'] = $query->getMessage()->getChat()->getRawData();
        } else {
            $update['message']['chat'] = [
                'id' => $update['message']['from']['id'],
                'first_name' => $update['message']['from']['first_name'],
                'username' => $update['message']['from']['username'],
                'type' => 'private'
            ];
        }

        $this->update = new Update($update, $this->telegram->getBotUsername());
        return $this->preExecute();
    }

    protected function removeNonPrivateMessage()
    {
        if ($this->getCallbackQuery()) {
            if (!$this->getMessage()->getChat()->isPrivateChat()) {
                return true;
            } else {
                return false;
            }
        }

        return parent::removeNonPrivateMessage();
    }
}
