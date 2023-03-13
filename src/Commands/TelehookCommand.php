<?php

namespace Vanloctech\Telehook\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Telegram\Bot\Objects\Message;
use Vanloctech\Telehook\AskTrait;
use Vanloctech\Telehook\Models\TelehookConversation;
use Vanloctech\Telehook\Models\TelehookConversationDetail;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookArgument;
use Vanloctech\Telehook\TelehookMetadata;
use Vanloctech\Telehook\TelehookSupport;

abstract class TelehookCommand
{
    use AskTrait;

    const FUNCTION_NAME_ASK = 'sendQuestion';
    const FUNCTION_NAME_VALIDATE = 'validate';
    const FUNCTION_NAME_BEFORE_STORE_ANSWER = 'beforeStoreAnswer';

    /**
     * @var Message|null|mixed Message receive from telegram
     */
    private $message = null;

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

    /**
     * @var Telehook telehook instance
     */
    protected $telehook = null;

    public function __construct($message = null)
    {
        $this->message = $message;
        if (!empty($this->message())) {
            $this->telehook = Telehook::init($this->message()->chat->id);
        }
    }

    /**
     * Response unknown command when receive command mismatch with commands in config/telehook.php
     *
     * @return array|bool|mixed
     */
    public function unknown()
    {
        $message = trans('telehook::messages.unknown_command');

        return Telehook::init($this->message->chat->id)->sendMessage($message);
    }

    public function typeNotMatch()
    {
        $message = trans('telehook::messages.type_not_match');

        return $this->telehook->sendMessage($message);
    }

    /**
     * Response not support chat type when receive command from 'group', 'suppergroup' and 'channel
     *
     * @return array|bool|mixed
     */
    public function doesntSupportChatType()
    {
        $message = trans('telehook::messages.doesnt_support_chat_type');

        return Telehook::init($this->message->chat->id)->sendMessage($message);
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
                $this->setConversation($this->start());
                $functionName = self::FUNCTION_NAME_ASK . $this->conversation->next_order_send_question;

                if (method_exists($this, $functionName)) {
                    $this->$functionName();

                    return 0;
                }

                $this->finish();

                return 0;
            } catch (\Throwable $exception) {
                report($exception);
                Telehook::init()
                    ->setChatId($this->message()->chat->id)
                    ->sendMessage('An error occurred');

                TelehookSupport::sendException($this->message()->chat->id, $exception);

                $this->stopping();
                return 0;
            }
        }

        try {
            $this->mappingArguments();
            $argumentName = $this->conversation->next_argument_name;
            $metadata = new TelehookMetadata($argumentName, $this->message());
            $metadata->setDirectory($this->conversation->argument_options['dir'] ?? null);
            $this->$argumentName = new TelehookArgument($metadata->get());
            $functionName = self::FUNCTION_NAME_VALIDATE . Str::ucfirst(Str::camel($argumentName));

            if (method_exists($this, $functionName)) {
                if (!$this->$functionName()) {
                    if ($this->isStop()) {
                        $this->stopping();

                        $this->stop();

                        return 0;
                    }

                    return 0;
                }
            }

            $functionName = self::FUNCTION_NAME_BEFORE_STORE_ANSWER . Str::ucfirst(Str::camel($argumentName));
            if (method_exists($this, $functionName)) {
                $this->$functionName();
            }

            $this->storeAnswer();

            $functionName = self::FUNCTION_NAME_ASK . ($this->conversation->next_order_send_question);

            if (method_exists($this, $functionName)) {
                $this->$functionName();

                return 0;
            }

            $this->finishing();
            $this->beforeFinish();
            $this->finish();

            return 0;
        } catch (\Throwable $exception) {
            report($exception);
            Telehook::init()
                ->setChatId($this->message()->chat->id)
                ->sendMessage('An error occurred');

            TelehookSupport::sendException($this->message()->chat->id, $exception);

            $this->stopping();
            return 0;
        }
    }

    /**
     * Start conversation
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    private function start()
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
//            'metadata' => '',
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
    private function stopping()
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
    private function storeAnswer()
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
                    'metadata' => json_encode($this->getMetadata()),
                    'type' => TelehookConversationDetail::TYPE_RECEIVE,
                    'argument_type' => $this->conversation->next_argument_type,
                ]);
            }

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Get metadata and store file if exists
     *
     * @return array
     */
    private function getMetadata(): array
    {
        $metadata = new TelehookMetadata($this->conversation->next_argument_name, $this->message());
        $metadata->setDirectory($this->conversation->argument_options['dir'] ?? null);

        return $metadata->setStoreFile(true)->get();
    }

    /**
     * Mapping argument before finish conversation
     *
     * @return void
     */
    private function finishing()
    {
        $this->mappingArguments();

        $this->conversation->update(['status' => TelehookConversation::STATUS_FINISH]);
    }

    private function mappingArguments()
    {
        $details = $this->conversation->detailsHasArgumentName();

        foreach ($details as $item) {
            $argName = $item->argument_name;
            $metadata = json_decode($item->metadata ?? '{}', true);
            $this->$argName = new TelehookArgument($metadata);
        }
    }

    /**
     * @param $key
     * @return null|TelehookArgument|mixed
     */
    public function __get($key)
    {
        if (!in_array($key, ['message', 'conversation', 'command', 'description'])) {
            /**
             * @var TelehookArgument
             */
            return $this->$key ?? null;
        }

        return $this->$key ?? null;
    }

    public function beforeFinish()
    {
        // override for handle or send message before finish conversation;
    }

    /**
     * Execute when prepare finish conversation
     *
     * @return void
     */
    abstract public function finish();
}
