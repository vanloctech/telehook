<?php

namespace Vanloctech\Telehook\Objects;

use Illuminate\Support\Str;

class Location
{
    /**
     * @var double
     */
    public $latitude;
    /**
     * @var double
     */
    public $longitude;
    /**
     * @var int
     */
    public $livePeriod = 0; // seconds

    public function __construct($data)
    {
        if ($data instanceof Location) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $key = Str::camel($key);
            $this->$key = $value;
        }

        return $this;
    }
}
