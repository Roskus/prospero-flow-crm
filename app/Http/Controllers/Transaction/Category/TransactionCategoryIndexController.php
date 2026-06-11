<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction\Category;

use App\Http\Controllers\MainController;
use App\Models\Transaction\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionCategoryIndexController extends MainController
{
    public function index(Request $request)
    {
        return view('transaction.category.index', [
            'categories' => Category::where('company_id', Auth::user()->company_id)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
