<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brand';

    public function getAll()
    {
        return Brand::orderBy('name', 'asc')->get();
    }
}
