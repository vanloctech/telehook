<?php

namespace Vanloctech\Telehook;

use Vanloctech\Telehook\Objects\Animation;
use Vanloctech\Telehook\Objects\Audio;
use Vanloctech\Telehook\Objects\Contact;
use Vanloctech\Telehook\Objects\Document;
use Vanloctech\Telehook\Objects\Location;
use Vanloctech\Telehook\Objects\Photo;
use Vanloctech\Telehook\Objects\Video;
use Vanloctech\Telehook\Objects\Voice;

class TelehookArgument
{
    const TYPE_TEXT = 'text';
    const TYPE_PHOTO = 'photo';
    const TYPE_VIDEO = 'video';
    const TYPE_DOCUMENT = 'document';
    const TYPE_AUDIO = 'audio';
    const TYPE_ANIMATION = 'animation';
    const TYPE_LOCATION = 'location';
    const TYPE_CONTACT = 'contact';
    const TYPE_VOICE = 'voice';

    /**
     * @var string name of argument
     */
    public $name;

    /**
     * @var string type of argument name
     */
    protected $type = self::TYPE_TEXT;

    /**
     * @var string|mixed|null message text
     */
    public $text = null;

    /**
     * @var Photo|null
     */
    public $photo = null;

    /**
     * @var Video|null
     */
    public $video = null;

    /**
     * @var Audio|null
     */
    public $audio = null;

    /**
     * @var Animation|null
     */
    public $animation = null;

    /**
     * @var Document|null
     */
    public $document = null;

    /**
     * @var Location|null
     */
    public $location = null;

    /**
     * @var Contact|null
     */
    public $contact = null;

    /**
     * @var Voice|null
     */
    public $voice = null;

    public function __construct($metadata)
    {
        $this->mappingMetadata($metadata);
    }

    /**
     * Mapping data into command
     *
     * @param $metadata
     * @return void
     */
    protected function mappingMetadata($metadata)
    {
        $this->name = $metadata['name'] ?? null;
        $this->type = $metadata['type'] ?? TelehookArgument::TYPE_TEXT;

        switch ($this->type) {
            case TelehookArgument::TYPE_TEXT:
                $this->text = $metadata['text'] ?? null;
                break;
            case TelehookArgument::TYPE_PHOTO:
                $this->photo = new Photo($metadata['photo']);
                break;
            case TelehookArgument::TYPE_VIDEO:
                $this->video = new Video($metadata['video']);
                break;
            case TelehookArgument::TYPE_ANIMATION:
                $this->animation = new Animation($metadata['animation']);
                break;
            case TelehookArgument::TYPE_DOCUMENT:
                $this->document = new Document($metadata['document']);
                break;
            case TelehookArgument::TYPE_AUDIO:
                $this->audio = new Audio($metadata['audio']);
                break;
            case TelehookArgument::TYPE_LOCATION:
                $this->location = new Location($metadata['location']);
                break;
            case TelehookArgument::TYPE_CONTACT:
                $this->contact = new Contact($metadata['contact']);
                break;
            case TelehookArgument::TYPE_VOICE:
                $this->voice = new Voice($metadata['voice']);
                break;
        }
    }

    /**
     * Determine if the message is of given type.
     *
     * @param string $type
     *
     * @return bool
     */
    protected function isType(string $type): bool
    {
        return $this->type == $type;
    }

    /**
     * Detect type
     *
     * @return string[]
     */
    protected function detectType(): array
    {
        return [
            'text',
            'photo',
            'audio',
            'video',
            'animation',
            'document',
            'location',
            'voice',
            'contact',
        ];
    }

    /**
     * Get type of object
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
