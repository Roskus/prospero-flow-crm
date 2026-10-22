<?php

declare(strict_types=1);

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BranchIndexController extends MainController
{
    public function index(): View
    {
        $branches = Branch::ofCompany((int) Auth::user()->company_id)
            ->orderByDesc('is_main')
            ->orderBy('code')
            ->get();

        return view('branch.index', ['branches' => $branches]);
    }
}
