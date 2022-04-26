<?php

namespace Vanloctech\Telehook\Console\Commands;

use Illuminate\Console\Command;
use Vanloctech\Telehook\Telehook;

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
        $commands = config('telehook.commands') ?? [];
        $commandSet = [];

        foreach ($commands as $class) {
            $classHandle = new $class();
            $commandSet[] = [
                'command' => $classHandle->getCommandName(),
                'description' => $classHandle->getDescription(),
            ];
        }

        if (config('telehook.command_menu', true)) {
            Telehook::init()->deleteMyCommands();
            Telehook::init()->setMyCommands($commandSet);
        }

        $this->info('Set command successfully.');
        return 0;
    }
}
