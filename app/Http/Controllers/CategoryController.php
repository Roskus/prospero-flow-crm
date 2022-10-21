<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends MainController
{
    public function index(Request $request)
    {
        $category = new Category();
        $data['categories'] = $category->getAllActiveByCompany(Auth::user()->company_id);

        return view('category.index', $data);
    }

    public function add()
    {
        $category = new Category();
        $data['category'] = $category;

        return view('category.category', $data);
    }

    public function edit(Request $request, int $id)
    {
        $category = Category::find($id);
        $data['category'] = $category;

        return view('category.category', $data);
    }

    public function save(Request $request)
    {
        if (empty($request->id)) {
            $request->validate([
                'name' => ['required', 'string', 'unique:category, company_id'],
            ]);

            $category = new Category();
        } else {
            $category = Category::find($request->id);

            $request->validate([
                'name' => [
                    'required',
                    'string',
                    Rule::unique('category')->ignore($category->id)
                ],
            ]);
        }
        $category->name = $request->name;
        $category->company_id = Auth::user()->company_id;
        $category->save();

        return redirect('/category');
    }
}
