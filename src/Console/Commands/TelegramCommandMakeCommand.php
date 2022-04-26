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

    public function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

            return false;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        // add command to config/telehook.php
        $oldCommands = config('telehook.commands');
        $config['commands'] = array_merge($oldCommands, [
            $name . '::class,',
        ]);
        Config::set('telehook', $config);

        $this->info($this->type.' created successfully.');
    }
}
