<?php

namespace Vanloctech\Telehook\Commands;

class StopTelehookCommand extends TelehookCommand
{
    protected $command = 'stop';

    // Handle when stop command is called
    protected $description = 'Stop conversation';

    public function __construct($message)
    {
        parent::__construct($message);
    }

    /**
     * @return void
     */
    public function stop()
    {
        parent::stop();
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
