<?php

namespace Vanloctech\Telehook\Commands;

use Vanloctech\Telehook\Telehook;

class DefaultTelegramCommand extends TelegramCommandAbstract
{
    public function __construct($chatId = '')
    {
        parent::__construct($chatId);
    }

    /**
     * Execute the command
     *
     * @return void
     */
    public function handle()
    {
//        Telehook::init($this->getChatId())->sendMessage('<b>Welcome to telehook.</b>');
    }
}
