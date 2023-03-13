<?php

namespace Vanloctech\Telehook\Objects;

use Vanloctech\Telehook\TelehookArgument;

class Video extends File
{
    public $width = null;
    public $height = null;
    /**
     * @var int|null
     */
    public $duration = null; // seconds
    protected $type = TelehookArgument::TYPE_VIDEO;
    protected $dir = 'telehook/videos';
    public $extension = null;

}
