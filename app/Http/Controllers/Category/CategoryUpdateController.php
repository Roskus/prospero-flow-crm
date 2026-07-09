<?php

declare(strict_types=1);

namespace App\Http\Controllers\Category;

use App\Http\Controllers\MainController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $category = Category::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $data['category'] = $category;

        return view('category.category', $data);
    }
}
