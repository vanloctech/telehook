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
    protected $signature = 'make:telehook {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new telehook command class';

    protected $type = 'TelehookCommand';

    protected function getNameInput(): string
    {
        return trim($this->argument('name')) . 'TelehookCommand';
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../stubs/telehook-command.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\TelehookCommand';
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            [
                'DummyCommand',
            ],
            [
                Str::lower(trim($this->argument('name'))),
            ],
            $stub
        );

        return parent::replaceNamespace($stub, $name);
    }
}
