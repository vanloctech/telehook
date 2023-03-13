<?php

namespace Vanloctech\Telehook\Objects;

use Vanloctech\Telehook\TelehookArgument;

class Animation extends File
{
    public $width = null;
    public $height = null;
    protected $type = TelehookArgument::TYPE_ANIMATION;
    protected $dir = 'telehook/animations';
    public $extension = null;

}
