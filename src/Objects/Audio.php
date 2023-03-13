<?php

namespace Vanloctech\Telehook\Objects;

use Vanloctech\Telehook\TelehookArgument;

class Audio extends File
{
    public $width = null;
    public $height = null;
    protected $type = TelehookArgument::TYPE_AUDIO;
    protected $dir = 'telehook/audios';
    public $extension = null;
    public $title = null;
    public $duration = null;

}
