<?php

namespace Vanloctech\Telehook\Commands;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\FileUpload\InputFile;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookArgument;

/**
 * Class Message.
 *
 * @property TelehookArgument $name Argument name
 * @property TelehookArgument $phone Argument phone number
 * @property TelehookArgument $avatar Argument avatar
 */
class ExampleTelehookCommand extends TelehookCommand
{
    protected $command = 'example';
    protected $description = 'Example command';

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function sendQuestion1()
    {
        $this->ask('name', 'What\'s your name?');
    }

    /**
     * Validate input of name
     *
     * "validate" is the prefix of the validate function, "Name" is argument name
     * @return bool
     */
    public function validateName(): bool
    {
        $firstChar = mb_substr($this->name->text, 0, 1);
        if ($firstChar != mb_strtoupper($firstChar)) {
            Telehook::init($this->message()->chat->id)
                ->sendMessage("The first letter of the name must be capitalized.\nPlease try something different");

            return false;
        }

        return true;
    }

    public function sendQuestion2()
    {
        $this->ask('phone', 'What\'s your phone number?');
    }

    public function validatePhone(): bool
    {
        $validate = Validator::make(
            ['phone' => $this->phone->text],
            ['phone' => ['digits:10']],
            [],
            ['phone' => 'phone number']
        );

        if ($validate->fails()) {
            $this->telehook
                ->sendMessage($validate->getMessageBag()->first() . "\nPlease try something different");

            return false;
        }

        return true;
    }

    public function sendQuestion3()
    {
        $this->askPhoto('avatar', 'Please upload a avatar.', [
            'dir' => 'photos', // default telehook/photos
            'disk' => 'public', // default public
        ]);
    }

    public function validateAvatar(): bool
    {
        if ($this->avatar->photo->width > 1024) {
            $this->telehook->sendMessage('Image too large. Try again');

            return false;
        }

        return true;
    }

    public function beforeStoreAnswerAvatar()
    {
        $this->telehook->sendMessage('Waiting for upload image...');
    }

    public function finish()
    {
        $this->telehook->sendMessage('Hi ' . $this->name->text . ', your phone number is ' . $this->phone->text);

        $this->telehook->sendMessage("Size of avatar: {$this->avatar->photo->width}x{$this->avatar->photo->height}");
        $this->telehook->sendPhoto([
            'photo' => InputFile::create(Storage::disk('public')->readStream('photos/' . $this->avatar->photo->fileName)),
//            'photo' => InputFile::create(Storage::disk('public')->readStream('telehook/photos/photo_AQADw7IxGx0juFd-_1677087905.jpg')), // send photo in local storage
//            'photo' => InputFile::create('https://www.teahub.io/photos/full/113-1139950_no-copyright.png'), // send photo with url
            'caption' => 'This is a image you uploaded! Supported by telehook package. ❤️😘😘',
        ]);
    }
}
