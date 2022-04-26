<?php

namespace Vanloctech\Telehook;

use Telegram\Bot\Api;
use Vanloctech\Telehook\Exceptions\TokenEmptyException;

class Telehook
{
    use TelehookApiTrait;

    private $apiUrl;

    public $telegram;

    public function __construct($chatId = null)
    {
        if (empty(config('telehook.token'))) {
            throw new TokenEmptyException();
        }

        $this->chatId = $chatId;
        $this->apiUrl = self::getApiUrl();
        $this->telegram = $this->initTelegramApi();
    }

    /**
     * Get telegram bots api url
     * @return string
     */
    public static function getApiUrl(): string
    {
        return config('telehook.api_url') . config('telehook.token') . '/';
    }

    public static function init($chatId = null): Telehook
    {
        return new static($chatId);
    }

    private function initTelegramApi(): Api
    {
        return new Api(config('telehook.token'));
    }
}
