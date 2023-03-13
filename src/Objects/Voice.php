<?php

namespace Vanloctech\Telehook\Objects;

use Vanloctech\Telehook\TelehookArgument;

class Voice extends File
{
    public $width = null;
    public $height = null;
    protected $type = TelehookArgument::TYPE_VOICE;
    protected $dir = 'telehook/voices';
    public $extension = null;
    public $duration = null;

}
