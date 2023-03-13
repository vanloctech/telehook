<?php

namespace Vanloctech\Telehook\Facades;

use Illuminate\Support\Facades\Facade;

class TelehookFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'telehook';
    }
}
