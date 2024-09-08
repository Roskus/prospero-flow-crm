<?php

declare(strict_types=1);

namespace App\Http\Controllers\Category;

use App\Http\Controllers\MainController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorySaveController extends MainController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $category = new Category;
        } else {
            $category = Category::find($request->id);
        }
        $category->name = $request->name;
        $category->company_id = (int) Auth::user()->company_id;
        $category->save();

        return redirect('/category');
    }
}
