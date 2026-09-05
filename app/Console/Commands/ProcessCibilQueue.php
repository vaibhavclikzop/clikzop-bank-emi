<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCibilJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessCibilQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-cibil-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process cibil files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $record = DB::table('loan_cibil_data_mst')
            ->where('status', 'processing')
            ->first();

        if (!$record) {
            $record = DB::table('loan_cibil_data_mst')
                ->where('status', 'pending')
                ->first();
        }

        if (!$record) {
            $this->info('No pending records found.');
            return;
        }

        ProcessCibilJob::dispatchSync($record->id);

        $this->info('Done');
    }
}
