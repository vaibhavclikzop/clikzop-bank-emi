<?php

namespace App\Console\Commands;

use App\Jobs\ProcessKarzaWebhookJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessKarzaWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-karza-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending Karza webhook files';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $webhooks = DB::table('webhooks')

            ->limit(10)
            ->where("processed", 0)
            ->get();

        foreach ($webhooks as $webhook) {

            $data =  ProcessKarzaWebhookJob::dispatchSync($webhook->id);
        }
    
        $this->info('Done');
    }
}
