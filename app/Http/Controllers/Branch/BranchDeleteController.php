<?php

declare(strict_types=1);

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BranchDeleteController extends MainController
{
    public function delete(int $id, BranchService $branchService): RedirectResponse
    {
        $branch = Branch::ofCompany((int) Auth::user()->company_id)->findOrFail($id);

        if (! $branchService->delete($branch)) {
            return redirect('/branch')->with(['status' => 'error', 'message' => __('The main branch cannot be deleted')]);
        }

        return redirect('/branch')->with(['status' => 'success', 'message' => __('Branch deleted successfully')]);
    }
}
