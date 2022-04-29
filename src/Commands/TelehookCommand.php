<?php

namespace Vanloctech\Telehook\Commands;

use Illuminate\Support\Facades\DB;
use Telegram\Bot\Objects\Message;
use Vanloctech\Telehook\Models\TelehookConversation;
use Vanloctech\Telehook\Models\TelehookConversationDetail;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookSupport;

abstract class TelehookCommand
{
    const FUNCTION_NAME_ASK = 'sendQuestion';
    /**
     * @var Message|null|mixed Message receive from telegram
     */
    protected $message = null;

    /**
     * @var string Command name
     */
    protected $command = '';

    /**
     * @var string Description of command
     */
    protected $description = '';

    /**
     * @var TelehookConversation|null|mixed Conversation model
     */
    protected $conversation = null;

    /**
     * @var bool Is stop command received
     */
    protected $isStop = false;

    public function __construct($message = null)
    {
        $this->message = $message;
    }

    /**
     * Response unknown command when receive command mismatch with commands in config/telehook.php
     *
     * @return array|bool|mixed
     */
    public function unknown()
    {
        $command = $this->getCommandName(true);

        $message = TelehookSupport::replaceKeyWithText('{command}', $command, TelehookSupport::getConfig('unknown_message'));

        return Telehook::init($this->message->chat->id)->sendMessage($message);
    }

    /**
     * Response not support chat type when receive command from 'group', 'suppergroup' and 'channel
     *
     * @return array|bool|mixed
     */
    public function doesntSupportChatType()
    {
        $message = TelehookSupport::getConfig('doesnt_support_chat_type_message');

        return Telehook::init($this->message->chat->id)->sendMessage($message);
    }

    /**
     * Response webhook busy when Exception is thrown
     *
     * @return array|bool|mixed
     */
    public function busy()
    {
        return Telehook::init($this->message->chat->id)->sendMessage(TelehookSupport::getConfig('busy_message'));
    }

    /**
     * @param mixed|TelehookConversation|null $conversation
     */
    public function setConversation($conversation): void
    {
        $this->conversation = $conversation;
    }

    /**
     * @param bool $isStop
     */
    public function setIsStop(bool $isStop): void
    {
        $this->isStop = $isStop;
    }

    /**
     * @return bool
     */
    public function isStop(): bool
    {
        return $this->isStop;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    /**
     * Get command name with character of command option
     *
     * @param bool $includeCharacter
     * @return string
     */
    public function getCommandName(bool $includeCharacter = false): string
    {
        if ($includeCharacter)
            return '/' . $this->command;

        return ltrim($this->command, '/');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Execute the command
     */
    public function handle()
    {
        if ($this->isStop()) {
            $this->stopping();

            $this->stop();

            return 0;
        }
        // check null for conversation will create new conversation and execute first function sendQuestion
        if (empty($this->conversation)) {
            try {
                $this->conversation = $this->start();
                $functionName = self::FUNCTION_NAME_ASK . $this->conversation->next_order_send_question;

                if (method_exists($this, $functionName)) {
                    $this->$functionName();

                    return 0;
                }

                $this->finish();

                return 0;
            } catch (\Throwable $exception) {
                logs()->error('Exception when starting TelehookConversation');
                report($exception);
                $this->busy();

                return 0;
            }
        }

        try {
            $this->handleAnswer();

            $functionName = self::FUNCTION_NAME_ASK . ($this->conversation->next_order_send_question);

            if (method_exists($this, $functionName)) {
                $this->$functionName();

                return 0;
            }

            $this->finishing();
            $this->finish();

            return 0;
        } catch (\Throwable $exception) {
            logs()->error('Exception when handling answer telehook command');
            report($exception);
            $this->busy();

            return 0;
        }
    }

    /**
     * Start conversation
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    protected function start()
    {
        DB::beginTransaction();
        try {
            // command is called for the first time of conversation
            $conversation = TelehookConversation::query()->create([
                'chat_id' => $this->message()->chat->id,
                'command_name' => $this->getCommandName(),
                'command_class' => get_called_class(),
                'status' => TelehookConversation::STATUS_START,
                'next_order_send_question' => 1,
                'created_at_bigint' => time(),
            ]);

            // save detail conversation with type is receive message from telegram
            TelehookConversationDetail::query()->create([
                'conversation_id' => $conversation->id,
                'message' => $this->message()->text,
                'payload' => json_encode($this->message()->all()),
//            'argument_name' => '',
//            'argument_value' => '',
                'type' => TelehookConversationDetail::TYPE_RECEIVE,
            ]);

            DB::commit();
            return $conversation;
        } catch (\Throwable $exception) {
            report($exception);
            DB::rollBack();

            return null;
        }
    }

    /**
     * Change status of conversation to stop
     * @return void
     */
    public function stopping()
    {
        $this->conversation->update([
            'status' => TelehookConversation::STATUS_STOP,
        ]);
    }

    /**
     * Execute stop command
     *
     * @return void
     */
    public function stop()
    {
//        Telehook::init($this->message->chat->id)->sendMessage('Stopped');
    }

    /**
     * Execute get answer from message
     *
     * @throws \Exception
     */
    protected function handleAnswer()
    {
        DB::beginTransaction();
        try {
            $this->conversation->update([
                'next_order_send_question' => $this->conversation->next_order_send_question + 1,
                'status' => TelehookConversation::STATUS_CHATTING,
            ]);

            if (!empty($this->conversation->next_argument_name)) {
                // create new record for detail conversation to set argument name and argument value
                TelehookConversationDetail::query()->create([
                    'conversation_id' => $this->conversation->id,
                    'message' => $this->message()->text,
                    'payload' => json_encode($this->message->all()),
                    'argument_name' => $this->conversation->next_argument_name,
                    'argument_value' => trim($this->message->text),
                    'type' => TelehookConversationDetail::TYPE_RECEIVE,
                ]);
            }

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            logs('daily')->debug('vo day');
            throw new \Exception($exception->getMessage());
        }

        // todo: xu ly nhap dau vao khong xac dinh
    }

    public function ask(string $message, string $argumentName)
    {
        if (!empty($message) && !empty($argumentName)) {
            Telehook::init($this->message->chat->id)->sendMessage($message);

            $this->conversation->update([
                'next_argument_name' => $argumentName,
            ]);
        }
    }

    protected function finishing()
    {
        $details = $this->conversation->detailsHasArgumentName;

        foreach ($details as $item) {
            $argName = $item->argument_name;
            $this->$argName = $item->argument_value ?? null;
        }

        $this->conversation->update(['status' => TelehookConversation::STATUS_FINISH]);
    }

    /**
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        return $this->$key ?? null;
    }

    /**
     * Execute when prepare finish conversation
     *
     * @return void
     */
    abstract public function finish();
}
