<?php

namespace Vanloctech\Telehook\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Telegram\Bot\Objects\Message;
use Vanloctech\Telehook\Commands\DefaultTelehookCommand;
use Vanloctech\Telehook\Commands\StopTelehookCommand;
use Vanloctech\Telehook\Models\TelehookConversation;
use Vanloctech\Telehook\TelehookSupport;

class TelehookController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $commands = TelehookSupport::getConfig('commands', []);
            $defaultClass = TelehookSupport::getConfig('default', DefaultTelehookCommand::class);
            $stopClass = TelehookSupport::getConfig('stop', StopTelehookCommand::class);

            logs('daily')->debug(json_encode($request->all()));
            $message = new Message($request->get('message'));

            if (!TelehookSupport::checkMessageDoesntSupport($message)) {
                return $this->responseSuccess();
            }

            $commandClass = null;
            $command = ltrim($message->text, '/');

            if (!$message->hasCommand() || !empty($commandClass = TelehookSupport::checkCommandName($stopClass, $command, $message))) {
                $conversation = TelehookConversation::query()
                    ->where('chat_id', $message->chat->id)
                    ->whereIn('status', TelehookConversation::statusChatting())
                    ->latest()
                    ->first();

                if ($conversation) {
                    if (!empty($commandClass)) {
                        $commandClass->setIsStop(true);
                    } else {
                        $commandClass = $conversation->command_class;
                        $commandClass = new $commandClass($message);
                    }
                    $commandClass->setConversation($conversation);
                } else {
                    $commandClass = new $defaultClass($message);
                    $commandClass->unknown();
                    return $this->responseSuccess();
                }
            } else {
                foreach ($commands as $class) {
                    if (!empty($commandClass = TelehookSupport::checkCommandName($class, $command, $message))) {
                        break;
                    }
                }
            }

            if (empty($commandClass)) {
                // todo: handle unknown command
                $commandClass = new $defaultClass($message);
                $commandClass->unknown();
                return $this->responseSuccess();
            }

            $commandClass->handle();

            return $this->responseSuccess();
        } catch (\Throwable $exception) {
            report($exception);

            return $this->responseSuccess();
        }
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
