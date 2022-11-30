<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class ProductImportSaveController extends MainController
{
    public function save(Request $request)
    {
        if (! $request->hasFile('upload')) {
            return redirect('/lead')->withErrors(__("Upload file can't be in blank"));
        }

        $file = $request->file('upload');
        $extension = $file->getClientOriginalExtension();

        return redirect('/product'); //->with($data);
    }
}
