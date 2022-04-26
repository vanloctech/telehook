<?php

namespace Vanloctech\Telehook\Commands;

use Vanloctech\Telehook\Telehook;

class HelpTelegramCommand extends TelegramCommandAbstract
{
    protected $command = 'help';

    protected $description = 'Display list of the bot\'s command';

    /**
     * @return void
     */
    public function handle()
    {
        $message = '';
        $commands = config('telehook.commands') ?? [];

        foreach ($commands as $class) {
            $classHandle = new $class();
            $message .= $classHandle->getCommandName(true) . ' - ' .
                implode(' ', $classHandle->getArgs()) .
                $classHandle->getDescription() . " \n ";
        }

        Telehook::init($this->getChatId())->sendMessage($message);
    }
}
