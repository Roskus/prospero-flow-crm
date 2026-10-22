<?php

declare(strict_types=1);

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class BranchCreateController extends MainController
{
    public function create(BranchService $branchService): View
    {
        $branch = new Branch(['code' => $branchService->nextCode((int) Auth::user()->company_id)]);

        return view('branch.branch', [
            'branch' => $branch,
            'countries' => Country::orderBy('name')->get(),
        ]);
    }
}
