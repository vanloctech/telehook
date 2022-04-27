<?php

namespace Vanloctech\Telehook;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait TelehookApiTrait
{
    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var array|string|integer
     */
    private $chatId = null;


    /**
     * Setup chat_id params
     *
     * @param $chatId
     * @return TelehookApiTrait|Telehook
     */
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;

        return $this;
    }

    /**
     * Send text messages
     *
     * @param string $message
     * @param array $paramsOption
     * @return array|bool|mixed
     */
    public function sendMessage(string $message, array $paramsOption = [])
    {
        if (!empty($message)) {
            $data = $paramsOption;
            if (!array_key_exists('parse_mode', $paramsOption)) {
                $data['parse_mode'] = 'html';
            }
            $data['chat_id'] = $this->chatId;
            $data['text'] = $message;

            return $this->telegram->sendMessage($data);
        }

        return false;
    }

    /** Send text messages for multiple user (chat_id)
     * @param string $message
     * @param array $paramsOption
     * @return bool|array|mixed
     */
    public function sendMessages(string $message, array $paramsOption = [])
    {
        if (!empty($message)) {
            $data = $paramsOption;
            $data['text'] = $message;

            $res = [];
            foreach ($this->chatId as $chatId) {
                $data['chat_id'] = $chatId;
                $response = $this->telegram->sendMessage($data);

                $res[] = $response;
            }

            return $res;
        }

        return true;
    }

    /**
     * Change the list of the bot's commands
     *
     * Example for param $command:
     *
     * [
     *      [
     *          "command": "hello",
     *          "description": "This description for /hello command"
     *      ],
     *      [..],
     * ]
     *
     * @param $commands
     * @param array $params
     * @return mixed
     */
    public function setMyCommands($commands, array $params = [])
    {
        try {
            $client = new Client();
            $data = [
                'commands' => $commands,
            ];
            $data = array_merge($data, $params);

            $response = $client->post($this->apiUrl . 'setMyCommands', [
                'json' => $data,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            logs()->error($exception->getResponse()->getBody());

            return json_decode($exception->getResponse(), true);
        }
    }

    /**
     * Delete the list of the bot's commands
     *
     * @param array $params
     * @return mixed
     */
    public function deleteMyCommands(array $params = [])
    {
        try {
            $client = new Client();
            $response = $client->post($this->apiUrl . 'deleteMyCommands', [
                'json' => $params,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            logs()->error($exception->getResponse()->getBody());
            return json_decode($exception->getResponse(), true);
        }
    }

    /**
     * Delete the list of the bot's commands
     *
     * @param array $params
     * @return mixed
     */
    public function deleteWebhook(array $params = [])
    {
        try {
            $client = new Client();
            $response = $client->post($this->apiUrl . 'deleteWebhook', [
                'json' => $params,
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (ClientException $exception) {
            logs()->error($exception->getResponse()->getBody());
            return json_decode($exception->getResponse(), true);
        }
    }
}
