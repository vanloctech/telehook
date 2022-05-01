<?php

namespace Vanloctech\Telehook\Commands;

use Illuminate\Support\Facades\Validator;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookArgument;

/**
 * Class Message.
 *
 * @property TelehookArgument $name Argument name
 * @property TelehookArgument $phone Argument phone number
 */
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
        $this->ask('name', 'What\'s your name?');
    }

    public function validate_nameLoc(): bool
    {
        $firstChar = mb_substr($this->name->value, 0, 1);
        if ($firstChar != mb_strtoupper($firstChar)) {
            Telehook::init($this->message()->chat->id)
                ->sendMessage("The first letter of the name must be capitalized.\nPlease try something different");

            return false;
        }

        return true;
    }

    public function sendQuestion2()
    {
        $this->ask('phone', 'What\'s your phone number?');
    }

    public function validatePhone(): bool
    {
        $validate = Validator::make(
            ['phone' => $this->phone->value],
            ['phone' => ['digits:10']],
            [],
            ['phone' => 'phone number']
        );

        if ($validate->fails()) {
            Telehook::init($this->message()->chat->id)
                ->sendMessage($validate->getMessageBag()->first() . "\nPlease try something different");

            return false;
        }

        return true;
    }

    public function finish()
    {
        Telehook::init($this->message()->chat->id)->sendMessage('Hi ' . $this->name->value . ', your phone number is ' . $this->phone->value);
    }
}
