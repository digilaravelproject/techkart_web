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
    protected $signature = 'affiliate:release-pending-commissions';

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
        $count = $walletTransactionService->releaseMaturedCommissions();

        $this->info("Successfully released {$count} matured affiliate commissions.");

        return 0;
    }
}
