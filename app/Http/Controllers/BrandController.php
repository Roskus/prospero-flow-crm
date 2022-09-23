<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends MainController
{
    public function index(Request $request)
    {
        $brand = new Brand();
        $data['brands'] = $brand->getAllByCompanyId(Auth::user()->company_id);

        return view('brand.index', $data);
    }

    public function add(Request $request)
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
        if (empty($request->id)) {
            $brand = new Brand();
        } else {
            $brand = Brand::find($request->id);
        }
        $brand->name = $request->name;
        $brand->company_id = Auth::user()->company_id;
        $brand->save();

        return redirect('/brand');
    }
}
