<?php

namespace Vanloctech\Telehook\Commands;

class DefaultTelehookCommand extends TelehookCommand
{
    public function __construct($message)
    {
        parent::__construct($message);
    }

    /**
     * Execute when prepare finish conversation
     *
     * @return void
     */
    public function finish()
    {
        // Implement finish() method.
    }
}
