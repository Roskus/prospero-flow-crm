<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends MainController
{
    public function index()
    {
        $brand = new Brand();
        $data['brands'] = $brand->getAll();
        return view('brand.index', $data);
    }

    public function add()
    {
        $brand = new Brand();
        $data['brand'] = $brand;
        return view('brand.brand', $data);
    }

    public function edit(Request $request, int $id)
    {
        $brand = Brand::find($id);
        $data['brand'] = $brand;
        return view('brand.brand', $data);
    }

    public function save(Request $request)
    {
        if(empty($request->id)) {
            $brand = new Brand();
        } else {
            $brand = Brand::find($request->id);
        }
        $brand->name = $request->name;
        $brand->save();
        return redirect('/brand');
    }
}
