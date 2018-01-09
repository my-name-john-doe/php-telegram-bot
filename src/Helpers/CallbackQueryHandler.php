<?php
namespace Longman\TelegramBot\Helpers;
use Longman\TelegramBot\Helpers\CallbackQueryHandlerInterface;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Telegram;

class CallbackQueryHandler
{
    protected static function executeCommand($command, Telegram $telegram)
    {
        $command_obj = $telegram->getCommandObject(strtolower($command));
        if ($command_obj && $command_obj->isEnabled() && ($command_obj instanceOf CallbackQueryHandlerInterface)) {
            return $command_obj->executeCallbackQuery();
        }
    }

    public static function handleCommand(CallbackQuery $query, Telegram $telegram)
    {
        if (preg_match('/^\/([^\s@]+)/', $query->getData(), $command)) {
            return self::executeCommand($command[1], $telegram);
        }
    }

    public static function handleConversation(CallbackQuery $query, Telegram $telegram)
    {
        if ($message = $query->getMessage()) {
            $conversation = new Conversation(
                $query->getFrom()->getId(),
                $message->getChat()->getId()
            );

            if ($conversation->exists() && ($command = $conversation->getCommand())) {
                return self::executeCommand($command, $telegram);
            }
        }
    }
}
