<?php

namespace Vanloctech\Telehook;

trait AskTrait
{
    /**
     * Check argument is valid
     * Does not support arguments which php property not support
     *
     * @param $name
     * @return false|int
     */
    public function checkArgumentIsValid($name)
    {
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name);
    }

    /**
     * Send question on telegram and get type text answer
     *
     * @param string $argumentName
     * @param string $message
     * @param string $type
     * @param array $options
     * @return void
     */
    public function ask(string $argumentName, string $message, string $type = TelehookArgument::TYPE_TEXT, array $options = [])
    {
        if (!empty($message) && !empty($argumentName)) {
            Telehook::init($this->message()->chat->id)->sendMessage($message);

            if ($this->checkArgumentIsValid($argumentName)) {
                $data = [
                    'next_argument_name' => $argumentName,
                    'next_argument_type' => $type,
                ];

                switch ($type) {
                    case TelehookArgument::TYPE_PHOTO:
                        $data['next_argument_options'] = [
                            'dir' => $options['dir'] ?? null,
                            'disk' => $options['disk'] ?? null,
                        ];
                        break;
                    case TelehookArgument::TYPE_TEXT:
                    default:
                        break;
                }

                $this->conversation->update($data);
            } else {
                Telehook::init($this->message()->chat->id)->sendMessage('Argument name is not a valid PHP variable name');
            }
        }
    }

    /**
     * Ask to get a photo
     *
     * @param string $argumentName
     * @param string $message
     * @param $options
     * <br>Options supported:
     * <br>- dir - the directory to store photo
     * <br>- disk - public, s3,...
     * @return void
     */
    public function askPhoto(string $argumentName, string $message, array $options = [])
    {
        $this->ask($argumentName, $message, TelehookArgument::TYPE_PHOTO, $options);
    }

    /**
     * Ask to get a video
     *
     * @param string $argumentName
     * @param string $message
     * @param $options
     * <br>Options supported:
     * <br>- dir - the directory to store photo
     * <br>- disk - public, s3,...
     * @return void
     */
    public function askVideo(string $argumentName, string $message, array $options = [])
    {
        $this->ask($argumentName, $message, TelehookArgument::TYPE_VIDEO, $options);
    }

    /**
     * Ask to get an audio
     *
     * @param string $argumentName
     * @param string $message
     * @param $options
     * <br>Options supported:
     * <br>- dir - the directory to store photo
     * <br>- disk - public, s3,...
     * @return void
     */
    public function askAudio(string $argumentName, string $message, array $options = [])
    {
        $this->ask($argumentName, $message, TelehookArgument::TYPE_AUDIO, $options);
    }

    /**
     * Ask to get a document
     *
     * @param string $argumentName
     * @param string $message
     * @param $options
     * <br>Options supported:
     * <br>- dir - the directory to store photo
     * <br>- disk - public, s3,...
     * @return void
     */
    public function askDocument(string $argumentName, string $message, array $options = [])
    {
        $this->ask($argumentName, $message, TelehookArgument::TYPE_DOCUMENT, $options);
    }

    /**
     * Ask to get an animation
     *
     * @param string $argumentName
     * @param string $message
     * @param $options
     * <br>Options supported:
     * <br>- dir - the directory to store photo
     * <br>- disk - public, s3,...
     * @return void
     */
    public function askAnimation(string $argumentName, string $message, array $options = [])
    {
        $this->ask($argumentName, $message, TelehookArgument::TYPE_ANIMATION, $options);
    }

    /**
     * Ask to get a contact
     *
     * @param string $argumentName
     * @param string $message
     */
    public function askContact(string $argumentName, string $message)
    {
        $this->ask($argumentName, $message, TelehookArgument::TYPE_CONTACT);
    }

    /**
     * Ask to get a voice
     *
     * @param string $argumentName
     * @param string $message
     * @param $options
     * <br>Options supported:
     * <br>- dir - the directory to store photo
     * <br>- disk - public, s3,...
     * @return void
     */
    public function askVoice(string $argumentName, string $message, array $options = [])
    {
        $this->ask($argumentName, $message, TelehookArgument::TYPE_VOICE, $options);
    }
}
