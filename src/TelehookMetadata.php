<?php

namespace Vanloctech\Telehook;

use Telegram\Bot\Objects\Message;
use Vanloctech\Telehook\Objects\Animation;
use Vanloctech\Telehook\Objects\Audio;
use Vanloctech\Telehook\Objects\Contact;
use Vanloctech\Telehook\Objects\Document;
use Vanloctech\Telehook\Objects\Location;
use Vanloctech\Telehook\Objects\Photo;
use Vanloctech\Telehook\Objects\Video;
use Vanloctech\Telehook\Objects\Voice;

class TelehookMetadata
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var null|string
     */
    public $disk = null;

    /**
     * @var null|string
     */
    public $dir = null;

    /**
     * @var Message
     */
    public $message;

    /**
     * @var bool
     */
    protected $storeFile = false;

    public function __construct($name, $message)
    {
        $this->name = $name;
        $this->message = $message;
    }

    /**
     * Set disk
     *
     * @param string|null $disk
     * @return $this
     */
    public function setDisk(string $disk = null): TelehookMetadata
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Format metadata for message type text
     *
     * @return array
     */
    protected function getMetadataByMessageTypeText(): array
    {
        return [
            'name' => $this->name,
            'text' => trim($this->message->text),
            'type' => TelehookArgument::TYPE_TEXT,
        ];
    }

    /**
     * Store a photo and format metadata for message type photo
     *
     * @return array
     */
    protected function getMetadataByMessageTypePhoto(): array
    {
        $photo = new Photo(
            array_merge(
                (array)$this->message->photo->last(), // get the highest quality
                [
                    'caption' => $this->message->caption,
                    'disk' => $this->disk,
                ]
            )
        );
        $photo->setDirectory($this->dir);

        if ($this->storeFile) {
            $photo->store();
        }

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_PHOTO,
            'photo' => $photo,
        ];
    }

    /**
     * Store a video and format metadata for message type video
     *
     * @return array
     */
    protected function getMetadataByMessageTypeVideo(): array
    {
        $video = new Video(
            array_merge(
                $this->message->video->toArray(),
                [
                    'caption' => $this->message->caption,
                    'disk' => $this->disk,
                ]
            )
        );
        $video->setDirectory($this->dir);

        if ($this->storeFile) {
            $video->store();
        }

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_VIDEO,
            'video' => $video,
        ];
    }

    /**
     * Store a audio and format metadata for message type audio
     *
     * @return array
     */
    protected function getMetadataByMessageTypeAudio(): array
    {
        $audio = new Audio(
            array_merge(
                $this->message->audio->toArray(),
                [
                    'caption' => $this->message->caption,
                    'disk' => $this->disk,
                ]
            )
        );
        $audio->setDirectory($this->dir);

        if ($this->storeFile) {
            $audio->store();
        }

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_AUDIO,
            'audio' => $audio,
        ];
    }

    /**
     * Document
     *
     * @return array
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    protected function getMetadataByMessageTypeDocument(): array
    {
        $document = new Document(
            array_merge(
                $this->message->document->toArray(),
                [
                    'caption' => $this->message->caption,
                    'disk' => $this->disk,
                ]
            )
        );
        $document->setDirectory($this->dir);

        if ($this->storeFile) {
            $document->store();
        }

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_DOCUMENT,
            'document' => $document,
        ];
    }

    /**
     * Animation
     *
     * @return array
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    protected function getMetadataByMessageTypeAnimation(): array
    {
        $animation = new Animation(
            array_merge(
                $this->message->animation->toArray(),
                [
                    'caption' => $this->message->caption,
                    'disk' => $this->disk,
                ]
            )
        );
        $animation->setDirectory($this->dir);

        if ($this->storeFile) {
            $animation->store();
        }

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_ANIMATION,
            'animation' => $animation,
        ];
    }

    /**
     * Location
     *
     * @return array
     */
    protected function getMetadataByMessageTypeLocation(): array
    {
        $location = new Location($this->message->location->toArray());

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_LOCATION,
            'animation' => $location,
        ];
    }

    /**
     * Contact
     * @return array
     */
    protected function getMetadataByMessageTypeContact(): array
    {
        $contact = new Contact($this->message->contact->toArray());

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_CONTACT,
            'contact' => $contact,
        ];
    }

    /**
     * Voice
     *
     * @return array
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    protected function getMetadataByMessageTypeVoice(): array
    {
        $voice = new Voice(
            array_merge(
                $this->message->voice->toArray(),
                [
                    'disk' => $this->disk,
                ]
            )
        );
        $voice->setDirectory($this->dir);

        if ($this->storeFile) {
            $voice->store();
        }

        return [
            'name' => $this->name,
            'type' => TelehookArgument::TYPE_VOICE,
            'voice' => $voice,
        ];
    }

    /**
     * Set store file or no
     *
     * @param $storeFile
     * @return $this
     */
    public function setStoreFile($storeFile = false): TelehookMetadata
    {
        $this->storeFile = $storeFile;

        return $this;
    }

    /**
     * Get metadata for mapping arguments
     *
     * @return array
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    public function get()
    {
        if ($this->message->isType(TelehookArgument::TYPE_TEXT)) {
            return $this->getMetadataByMessageTypeText();
        }

        if ($this->message->isType(TelehookArgument::TYPE_PHOTO)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypePhoto();
        }

        if ($this->message->isType(TelehookArgument::TYPE_VIDEO)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypeVideo();
        }

        if ($this->message->isType(TelehookArgument::TYPE_AUDIO)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypeAudio();
        }

        if ($this->message->isType(TelehookArgument::TYPE_DOCUMENT)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypeDocument();
        }

        if ($this->message->isType(TelehookArgument::TYPE_ANIMATION)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypeAnimation();
        }

        if ($this->message->isType(TelehookArgument::TYPE_LOCATION)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypeLocation();
        }

        if ($this->message->isType(TelehookArgument::TYPE_CONTACT)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypeContact();
        }

        if ($this->message->isType(TelehookArgument::TYPE_VOICE)) {
            return $this->setDisk($this->disk)->getMetadataByMessageTypeVoice();
        }

        return [];
    }

    /**
     * Set location to store file
     *
     * @param $dir
     * @return $this
     */
    public function setDirectory($dir = null): TelehookMetadata
    {
        if (!empty($dir)) {
            $this->dir = $dir;
        }

        return $this;
    }
}
