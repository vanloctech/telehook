<?php

namespace Vanloctech\Telehook\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vanloctech\Telehook\Commands\DefaultTelegramCommand;

class TelehookController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $characterOfCommand = config('telehook.character_command');
        $commands = config('telehook.commands');

        $data = $request->all();

        logs('daily')->debug(json_encode($data));
        if (empty($data['message'])) {
            // todo: handle for group with "my_chat_member"
            return $this->responseSuccess();
        }

        if (empty($data['message']['text'])) {
            // message with "group_chat_created"
            return $this->responseSuccess();
        }

        $message = $data['message']['text'];
        $chatId = $data['message']['chat']['id'];
        $args = explode(' ', trim($data['message']['text']));

        if ($args[0][0] != $characterOfCommand) {
            $commandClass = new DefaultTelegramCommand($chatId);
            $commandClass->handle();
            return $this->responseSuccess();
        }

        $command = ltrim(array_shift($args), $characterOfCommand);

        $commandClass = null;
        foreach ($commands as $class) {
            $classHandle = new $class($chatId);
            if ($classHandle->getCommandName() == $command) {
                $commandClass = $classHandle;
                break;
            }
        }

        if (empty($commandClass)) {
            $commandClass = new DefaultTelegramCommand($chatId);
            $commandClass->unknown();
            return $this->responseSuccess();
        }

        $argsRequire = $commandClass->getArgs(true);
        $commandClass->setData($data);
        $commandClass->setMessage($message);
        $commandClass->setChatId($chatId);

        // check missing argument
        if (count($args) < count($argsRequire)) {
            $commandClass->missingArgs();
            return $this->responseSuccess();
        }

        $commandClass->setArgs($args);

        $commandClass->handle();

        return $this->responseSuccess();

    }

    /**
     * @return JsonResponse
     */
    protected function responseSuccess(): JsonResponse
    {
        return response()
            ->json([
                'ok' => true,
            ]);
    }
}
