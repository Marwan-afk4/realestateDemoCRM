<?php

namespace App\Console\Commands;

use App\Services\Crm\InventoryService;
use App\Services\Crm\SaleDeskService;
use Illuminate\Console\Command;

class ReleaseExpiredHolds extends Command
{
    protected $signature = 'inventory:release-expired-holds';

    protected $description = 'Release EOI and reservation holds that have expired, and mark overdue buyer installments';

    public function handle(InventoryService $inventory, SaleDeskService $sales): int
    {
        $released = $inventory->releaseExpired();
        $overdue = $sales->markOverdue();

        $this->info("Released {$released} expired holds. Marked {$overdue} installment rows.");

        return self::SUCCESS;
    }
}
