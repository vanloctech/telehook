<?php

namespace Vanloctech\Telehook\Objects;

use Vanloctech\Telehook\TelehookArgument;

class Document extends File
{
    public $width = null;
    public $height = null;
    protected $type = TelehookArgument::TYPE_DOCUMENT;
    protected $dir = 'telehook/docs';
    public $extension = null;

}
