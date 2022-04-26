<?php

namespace Vanloctech\Telehook\Facades;

use Illuminate\Support\Facades\Facade;

class TelehookFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'telehook';
    }
}
