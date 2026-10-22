<?php

namespace Webkul\Affiliate\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Affiliate\Services\WalletTransactionService;

class ReleasePendingCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate:release-pending-commissions {--force : Force release all pending commissions immediately}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release cleared pending commissions into available wallet balance once return window expires';

    /**
     * Execute the console command.
     */
    public function handle(WalletTransactionService $walletTransactionService): int
    {
        $force = (bool) $this->option('force');

        $count = $walletTransactionService->releaseMaturedCommissions($force);

        $this->info("Successfully released {$count} affiliate commissions into available wallet balances.");

        return 0;
    }
}
