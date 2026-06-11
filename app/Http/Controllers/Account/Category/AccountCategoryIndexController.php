<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account\Category;

use App\Http\Controllers\MainController;
use App\Models\Account\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountCategoryIndexController extends MainController
{
    public function index(Request $request)
    {
        return view('account.category.index', [
            'categories' => Category::where('company_id', Auth::user()->company_id)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
