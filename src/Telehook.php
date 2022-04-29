<?php

namespace Vanloctech\Telehook;

use Telegram\Bot\Api;
use Vanloctech\Telehook\Exceptions\TelehookTokenEmptyException;

class Telehook
{
    use TelehookApiTrait;

    protected $apiUrl;

    protected $telegramApi;

    public function __construct($chatId = null)
    {
        if (empty(TelehookSupport::getConfig('token'))) {
            throw new TelehookTokenEmptyException();
        }

        $this->chatId = $chatId;
        $this->apiUrl = self::getApiUrl();
        $this->telegramApi = $this->initTelegramApi();
    }

    /**
     * Get telegram bots api url
     * @return string
     */
    public static function getApiUrl(): string
    {
        return TelehookSupport::getConfig('api_url') . TelehookSupport::getConfig('token') . '/';
    }

    public static function init($chatId = null): Telehook
    {
        return new static($chatId);
    }

    private function initTelegramApi(): Api
    {
        return new Api(TelehookSupport::getConfig('token'));
    }

    /**
     * Instances of bot telegram api
     *
     * @return Api
     */
    public function telegramApi(): Api
    {
        return $this->telegramApi;
    }
}
