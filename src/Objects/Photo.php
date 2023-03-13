<?php

namespace Vanloctech\Telehook\Objects;

use Vanloctech\Telehook\TelehookArgument;

class Photo extends File
{
    public $width = null;
    public $height = null;
    protected $type = TelehookArgument::TYPE_PHOTO;
    protected $dir = 'telehook/photos';
    public $extension = 'jpg';

}
