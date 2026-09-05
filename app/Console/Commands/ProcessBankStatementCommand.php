<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBankStatementJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessBankStatementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-bank-statement-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process bank statement';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          $record = DB::table('loan_banking_mst')
            ->where('status', 'processing')
            ->first();

        if (!$record) {
            $record = DB::table('loan_banking_mst')
                ->where('status', 'pending')
                ->first();
        }

        if (!$record) {
            $this->info('No pending records found.');
            return;
        }

        ProcessBankStatementJob::dispatchSync($record->id);

        $this->info('Done');
    }
}
