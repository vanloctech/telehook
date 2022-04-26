<?php

namespace Vanloctech\Telehook\Commands;

use Illuminate\Support\Str;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookSupport;

abstract class TelegramCommandAbstract
{
    protected $chatId;

    protected $args = [];

    protected $data = [];

    protected $message = '';

    protected $command = '';

    protected $description = '';

    public function __construct($chatId = '')
    {
        $this->chatId = $chatId;
    }

    public function unknown()
    {
        $command = $this->getCommandName(true);

        $message = TelehookSupport::replaceKeyWithText('{command}', $command, config('telehook.unknown_message'));

        return Telehook::init($this->chatId)->sendMessage($message);
    }

    public function missingArgs()
    {
        $command = $this->getCommandName(true);


        $message = TelehookSupport::replaceKeyWithText('{command}', $command, config('telehook.missing_args_message'));

        return Telehook::init($this->chatId)->sendMessage($message);
    }

    /**
     * Execute the command
     *
     * @return void
     */
    abstract public function handle();

    /**
     * @return array|string
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * @param array|string $chatId
     */
    public function setChatId($chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * @param array $argsProperty property of arguments
     * @param array $args argument value
     */
    public function setArgs(array $args): void
    {
        $argsProperty = $this->getArgs();

        foreach ($argsProperty as $i => $arg) {
            if (!empty($args[$i])) {
                $this->$arg = $args[$i];
            } else {
                $this->$arg = null;
            }
        }
    }

    /**
     * @param bool $onlyRequired
     * @return array
     */
    public function getArgs(bool $onlyRequired = false): array
    {
        $args = [];

        foreach ($this->args as $arg) {
            if (mb_substr($arg, -1) != '?') {
                $args[] = $arg;
            } else if (!$onlyRequired) {
                $args[] = mb_substr($arg, 0, mb_strlen($arg) - 1);
            }
        }

        return $args;
    }

    /**
     * Get "argument" value
     *
     * @param string $argName
     * @param mixed $default
     * @return mixed
     */
    public function get(string $argName, $default = null)
    {
        if (in_array($argName, $this->getArgs())) {
            return $this->$argName ?? null;
        }

        return $default;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Get command name with character of command option
     *
     * @param bool $inlcudeCharacter
     * @return string
     */
    public function getCommandName(bool $includeCharacter = false): string
    {
        if ($includeCharacter)
            return config('telehook.character_command') . $this->command;

        return $this->command;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

}
