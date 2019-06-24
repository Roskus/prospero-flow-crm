<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $category = new Category();
        $data['categories'] = $category->getAll();
        return view('category.index',$data);
    }

    public function add()
    {
        $category = new Category();
        $data['category'] = $category;
        return view('category.category',$data);
    }

    public function edit(Request $request, int $id)
    {
        $category = Category::find($id);
        $data['category'] = $category;
        return view('category.category',$data);
    }

    public function save(Request $request)
    {
        if(empty($request->id)) {
            $category = new Category();
        } else {
            $category = Category::find($request->id);
        }
        $category->name = $request->name;
        $category->save();
        return redirect('/category');
    }
}
