<?php

namespace Vanloctech\Telehook;

use Telegram\Bot\Objects\Message;
use Vanloctech\Telehook\Commands\DefaultTelehookCommand;

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
        if (
            !$message->isType('text') &&
            !$message->isType('photo')
        ) {
            logs()->error('cc');
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
     * @return mixed|null instance class or null
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
     * @return mixed
     */
    public static function getConfig(string $key, $default = null)
    {
        return config('telehook.' . $key, $default);
    }
}
