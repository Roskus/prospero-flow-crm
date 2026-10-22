<?php

declare(strict_types=1);

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BranchMainController extends MainController
{
    public function makeMain(int $id, BranchService $branchService): RedirectResponse
    {
        $branch = Branch::ofCompany((int) Auth::user()->company_id)->findOrFail($id);

        if (! $branchService->makeMain($branch)) {
            return redirect('/branch')->with(['status' => 'error', 'message' => __('The branch no longer exists')]);
        }

        return redirect('/branch')->with(['status' => 'success', 'message' => __('Main branch changed successfully')]);
    }
}
