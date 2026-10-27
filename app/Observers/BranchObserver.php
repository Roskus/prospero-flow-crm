<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Branch;
use App\Services\CashAccountService;

class BranchObserver
{
    public function __construct(private CashAccountService $cashAccountService) {}

    public function created(Branch $branch): void
    {
        $this->cashAccountService->createForBranch($branch);
    }
}
