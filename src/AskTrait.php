<?php

namespace Vanloctech\Telehook;

trait AskTrait
{

    public function checkArgumentIsValid($name)
    {
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name);
    }

    /**
     * Send question on telegram and get type text answer
     *
     * @param string $message
     * @param string $argumentName
     * @return void
     */
    public function ask(string $argumentName, string $message)
    {
        if (!empty($message) && !empty($argumentName)) {
            Telehook::init($this->message()->chat->id)->sendMessage($message);

            if ($this->checkArgumentIsValid($argumentName)) {
                $this->conversation->update([
                    'next_argument_name' => $argumentName,
                ]);
            } else {
                Telehook::init($this->message()->chat->id)->sendMessage('Argument name is not a valid PHP variable name');
            }
        }
    }

//    public function askPhoto(string $argumentName, string $message)
//    {
//
//    }
}
