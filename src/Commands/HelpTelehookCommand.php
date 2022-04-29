<?php

namespace Vanloctech\Telehook\Commands;

use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookSupport;

class HelpTelehookCommand extends TelehookCommand
{
    protected $command = 'help';

    protected $description = 'Display list of the bot\'s command';

    /**
     * @return int
     */
    public function handle()
    {
        $message = '';
        $commands = TelehookSupport::getConfig('commands', []);
        $stopClass = TelehookSupport::getConfig('stop', StopTelehookCommand::class);
        $commands[] = $stopClass;

        foreach ($commands as $class) {
            $classHandle = new $class($this->message());
            $message .= $classHandle->getCommandName(true) . ' - ' .
                $classHandle->getDescription() . " \n ";
        }

        Telehook::init($this->message()->chat->id)->sendMessage($message);

        return 0;
    }

    /**
     */
    public function finish()
    {
        // Implement finish() method.
    }
}
