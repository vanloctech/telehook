<?php

namespace Vanloctech\Telehook\Console\Commands;

use Illuminate\Console\Command;
use Vanloctech\Telehook\Models\TelehookConversation;
use Vanloctech\Telehook\TelehookSupport;

class StopTelehookConversationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telehook:stop-conversation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop telehook conversation after 10 minutes';

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
        $limitTime = TelehookSupport::getConfig('limited_time_conversation');

        $timeCompare = time() - $limitTime * 60;
        $conversations = TelehookConversation::query()
            ->whereIn('status', TelehookConversation::statusChatting())
            ->where('created_at_bigint', '<=', $timeCompare)
            ->update([
                'status' => TelehookConversation::STATUS_STOP,
            ]);

        $this->info('Stop conversation successfully.');

        return 0;
    }
}
