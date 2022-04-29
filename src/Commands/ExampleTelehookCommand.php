<?php

namespace Vanloctech\Telehook\Commands;

use Vanloctech\Telehook\Telehook;

class ExampleTelehookCommand extends TelehookCommand
{
    protected $command = 'example';
    protected $description = 'Example command';

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function sendQuestion1()
    {
        $this->ask('What\'s your name?', 'name');
    }

    public function sendQuestion2()
    {
        $this->ask('What\'s your phone number?', 'phone');
    }

    public function finish()
    {
        Telehook::init($this->message()->chat->id)->sendMessage('Hi ' . $this->name . ', your phone number is ' . $this->phone);
    }
}
