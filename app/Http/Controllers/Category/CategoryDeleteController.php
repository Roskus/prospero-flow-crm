<?php

declare(strict_types=1);

namespace App\Http\Controllers\Category;

use App\Http\Controllers\MainController;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $category = Category::find($id);
        $category->delete();

        return redirect('category');
    }
}
