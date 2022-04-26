<?php

namespace Vanloctech\Telehook;

class TelehookSupport
{
    public static function replaceKeyWithText($search, $replace, $subject)
    {
        $segments = explode($search, $subject);

        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= $replace . $segment;
        }

        return $result;
    }
}
