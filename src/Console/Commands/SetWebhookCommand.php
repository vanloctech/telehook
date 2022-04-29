<?php

namespace Vanloctech\Telehook\Console\Commands;

use Illuminate\Console\Command;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookSupport;

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
        // todo: moi vo co the khong co webhook
        if ($response['ok']) {
            try {
                $telehook->telegramApi()->setWebhook(TelehookSupport::getConfig('set_webhook'));

                $this->info('Your URI webhook: ' . TelehookSupport::getConfig('set_webhook.url'));
                $this->info('Set webhook successfully.');

                return 0;
            } catch (\Exception $exception) {

                $this->error('Set webhook failed.');
                $this->error($response->getBody());

                return 0;
            }
        }

        $this->error('Cannot delete webhook.');
        return 0;
    }
}
