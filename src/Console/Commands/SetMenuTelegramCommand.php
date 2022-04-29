<?php

namespace Vanloctech\Telehook\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Vanloctech\Telehook\Commands\StopTelehookCommand;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookSupport;

class SetMenuTelegramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telehook:set-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the list of the bot\'s commands';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $commands = TelehookSupport::getConfig('commands', []);
        $stopClass = TelehookSupport::getConfig('stop', StopTelehookCommand::class);
        $commands[] = $stopClass;
        $commandSet = [];

        foreach ($commands as $class) {
            $classHandle = new $class(null);
            if ($classHandle->getCommandName() != Str::lower($classHandle->getCommandName())) {
                $this->error('Command name required is lower string.');
                $this->error($classHandle->getCommandName() . ' is not lower');
                return 0;
            }
            $commandSet[] = [
                'command' => $classHandle->getCommandName(),
                'description' => $classHandle->getDescription(),
            ];
        }

        Telehook::init()->deleteMyCommands();
        $response = Telehook::init()->setMyCommands($commandSet);
        if (!$response['ok']) {
            $this->error('Set command failed.');
            $this->error(json_encode($response));

            return 0;
        }

        $this->info('Set command successfully.');
        return 0;
    }
}
