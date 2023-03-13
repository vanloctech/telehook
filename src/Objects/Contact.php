<?php

namespace Vanloctech\Telehook\Objects;

use Illuminate\Support\Str;

class Contact
{
    /**
     * @var string
     */
    public $phoneNumber;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        if ($data instanceof Contact) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $key = Str::camel($key);
            $this->$key = $value;
        }

        return $this;
    }
}
