<?php

namespace Vanloctech\Telehook\Console\Commands;

use Illuminate\Console\Command;
use Vanloctech\Telehook\Telehook;

class SetWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telehook:set-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Specify a url and receive incoming updates via an outgoing webhook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $telehook = Telehook::init();
        $response = $telehook->deleteWebhook();
        if ($response['ok']) {
            $response = $telehook->telegram->setWebhook(config('telehook.set_webhook'));

            if ($response['ok']) {
                $this->info('Your URI webhook: ' . config('telehook.set_webhook.url'));
                $this->info('Set webhook successfully.');

                return 0;
            }

            $this->error('Set webhook failed.');
            $this->error(json_encode($response));
        }

        $this->error('Cannot delete webhook.');
        return 0;
    }
}
