<?php

namespace Vanloctech\Telehook;

use Telegram\Bot\Objects\Message;
use Vanloctech\Telehook\Commands\DefaultTelehookCommand;
use Vanloctech\Telehook\Commands\TelehookCommand;

class TelehookSupport
{
    /**
     * Convert key into plain text
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceKeyWithText(string $search, string $replace, string $subject): string
    {
        $segments = explode($search, $subject);

        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= $replace . $segment;
        }

        return $result;
    }

    /**
     * Check message does not support
     *
     * @param Message $message
     * @return bool
     */
    public static function checkMessageDoesntSupport(Message $message): bool
    {
        $defaultClass = config('telehook.default', DefaultTelehookCommand::class);
        if (in_array($message->chat->type, ['group', 'channel', 'supergroup'])) {
            $commandClass = new $defaultClass($message);
            $commandClass->doesntSupportChatType();

            return false;
        }

        $flag = false;
        foreach (self::typesSupport() as $type) {
            if ($message->isType($type)) {
                $flag = true;
                break;
            }
        }

        if (!$flag) {
            return false;
        }

        if (
            $message->isType('group_chat_created') ||
            $message->isType('supergroup_chat_created') ||
            $message->isType('channel_chat_created')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check command name base on class name
     *
     * @param string $className
     * @param string $commandName
     * @param Message $message
     * @param bool $isInstance
     * @return TelehookCommand|mixed|null instance class or null
     */
    public static function checkCommandName(string $className, string $commandName, Message $message, $isInstance = true)
    {
        $res = null;

        $classHandle = new $className($message);
        if ($classHandle->getCommandName() == $commandName) {
            if ($isInstance) {
                $res = $classHandle;
            } else {
                $res = $className;
            }
        }

        return $res;
    }

    /**
     * Get config of telehook by key
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public static function getConfig(string $key, $default = null)
    {
        return config('telehook.' . $key, $default);
    }

    /**
     * Type allows message
     *
     * @return array
     */
    public static function typesSupport(): array
    {
        return [
            TelehookArgument::TYPE_TEXT,
            TelehookArgument::TYPE_PHOTO,
            TelehookArgument::TYPE_VIDEO,
            TelehookArgument::TYPE_ANIMATION,
            TelehookArgument::TYPE_AUDIO,
            TelehookArgument::TYPE_DOCUMENT,
            TelehookArgument::TYPE_LOCATION,
            TelehookArgument::TYPE_CONTACT,
            TelehookArgument::TYPE_VOICE,
        ];
    }

    /**
     * Send exception with local or stagin environment
     *
     * @param $chatId
     * @param \Throwable $exception
     * @return void
     */
    public static function sendException($chatId, \Throwable $exception)
    {
        if (app()->environment(['local', 'staging'])) {
            Telehook::init($chatId)->sendMessage($exception->getMessage() . ' at ' . $exception->getFile() . ':' . $exception->getLine());
        }
    }
}
