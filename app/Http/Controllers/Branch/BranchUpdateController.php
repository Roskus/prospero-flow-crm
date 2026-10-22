<?php

declare(strict_types=1);

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class BranchUpdateController extends MainController
{
    public function update(int $id): View
    {
        return view('branch.branch', [
            'branch' => Branch::ofCompany((int) Auth::user()->company_id)->findOrFail($id),
            'countries' => Country::orderBy('name')->get(),
        ]);
    }
}
