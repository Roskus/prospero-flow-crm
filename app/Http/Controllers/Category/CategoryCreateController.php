<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\MainController;
use App\Models\Category;

class CategoryCreateController extends MainController
{
    public function create()
    {
        $category = new Category();
        $data['category'] = $category;

        return view('category.category', $data);
    }
}
