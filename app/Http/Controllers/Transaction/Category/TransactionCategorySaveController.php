<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction\Category;

use App\Http\Controllers\MainController;
use App\Models\Transaction\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionCategorySaveController extends MainController
{
    public function save(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ]);

        $category = empty($request->id)
            ? new Category
            : Category::where('company_id', Auth::user()->company_id)->findOrFail($request->id);

        $category->company_id = Auth::user()->company_id;
        $category->name = $request->name;
        $category->save();

        return redirect('/transaction/category');
    }
}
