<?php

namespace Vanloctech\Telehook\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class TelegramCommandMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:telegram-command {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new telegram command class';

    protected $type = 'TelegramCommand';

    protected function getNameInput(): string
    {
        return trim($this->argument('name')) . 'TelegramCommand';
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../stubs/telegram-command.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\TelegramCommand';
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            [
                'DummyCommand',
            ],
            [
                Str::kebab(trim($this->argument('name'))),
            ],
            $stub
        );

        return parent::replaceNamespace($stub, $name);
    }
}
