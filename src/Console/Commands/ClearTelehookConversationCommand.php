<?php

namespace Vanloctech\Telehook\Console\Commands;

use Illuminate\Console\Command;
use Vanloctech\Telehook\Models\TelehookConversation;
use Vanloctech\Telehook\TelehookSupport;

class ClearTelehookConversationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telehook:clear {--chunk=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear telehook conversations';

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
    public function handle()
    {
        $keepDays = TelehookSupport::getConfig('keep_conversations_finished_for_days');
        $timeCompare = now()->subDays($keepDays)->getTimestamp();
        $chunk = $this->option('chunk') ?? 1000;

        do {
            $count = TelehookConversation::query()
                ->whereIn('status', TelehookConversation::statusFinish())
                ->where('created_at_bigint', '<=', $timeCompare)
                ->limit($chunk)
                ->delete();

        } while ($count > 0);

        $this->info('Clear conversation successfully.');

        return 0;
    }
}
