<?php

declare(strict_types=1);

namespace App\Http\Controllers\Category;

use App\Http\Controllers\MainController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryIndexController extends MainController
{
    public function index(Request $request)
    {
        $category = new Category();
        $data['categories'] = $category->getAllActiveByCompanyPaginated(Auth::user()->company_id);

        return view('category.index', $data);
    }
}
