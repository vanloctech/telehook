<?php

namespace Vanloctech\Telehook;

use Telegram\Bot\Api;
use Vanloctech\Telehook\Exceptions\TelehookTokenEmptyException;

/**
 * @method sendPhoto(array $params)
 * @method sendAudio(array $params)
 * @method sendDocument(array $params)
 * @method sendVideo(array $params)
 * @method sendAnimation(array $params)
 * @method sendVoice(array $params)
 * @method sendVideoNote(array $params)
 */
class Telehook
{
    use TelehookApiTrait;

    const API_URL = 'https://api.telegram.org';

    protected $apiUrl;

    /**
     * @var Api $telegramApi
     */
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
        return self::API_URL . '/bot' . TelehookSupport::getConfig('token') . '/';
    }

    /**
     * Get api file url
     *
     * @return string
     */
    public static function getApiFileUrl(): string
    {
        return self::API_URL . '/file/bot' . TelehookSupport::getConfig('token') . '/';
    }

    /**
     * Init object
     *
     * @param $chatId
     * @return Telehook
     * @throws TelehookTokenEmptyException
     */
    public static function init($chatId = null): Telehook
    {
        return new static($chatId);
    }

    /**
     * Init bot telegram api with telehook
     *
     * @return Api
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
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
