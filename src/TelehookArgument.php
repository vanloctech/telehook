<?php

namespace Vanloctech\Telehook;

class TelehookArgument
{
    /**
     * @var string name of argument
     */
    public $name;

    /**
     * @var string|int value input
     */
    public $value;

    /**
     * @var string type of argument name
     */
    public $type = 'text';

    /**
     * @var null disk of storage ['public', 's3', ...]
     */
    public $disk = null;

    public function __construct($metadata = [])
    {
        $this->mappingMetadata($metadata);
    }

    protected function mappingMetadata($metadata)
    {
        $this->name = $metadata['name'] ?? null;
        $this->value = $metadata['value'] ?? null;
        $this->type = $metadata['type'] ?? 'text';
        $this->disk = $metadata['disk'] ?? null;
    }

    /**
     * Determine if the message is of given type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isType(string $type): bool
    {
        return $this->type == $type;
    }

    /**
     * Detect type
     *
     * @return string[]
     */
    public function detectType(): array
    {
        return [
            'text',
            'photo',
//            'audio',
//            'video',
//            'video_note',
//            'document',
//            'voice',
//            'contact',
//            'location',
        ];
    }
}
