<?php

namespace Vanloctech\Telehook\Commands;

use Vanloctech\Telehook\Telehook;

class ExampleTelegramCommand extends TelegramCommandAbstract
{
    protected $command = 'example';
    protected $description = 'Example command';

    protected $args = [
        'name',
        'email?',
    ];

    public function __construct($chatId = '')
    {
        parent::__construct($chatId);
    }

    public function handle()
    {
        Telehook::init($this->getChatId())->sendMessage('Hi ' . $this->get('name') . ', your email is ' . $this->get('email'));
    }
}
