<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\MainController;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $category = Category::find($id);
        $data['category'] = $category;

        return view('category.category', $data);
    }

}
