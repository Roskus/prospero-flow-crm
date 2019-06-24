<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const ACTIVE = 1;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'category';

    public function getAll()
    {
        return Category::orderBy('name','asc')->get();
    }
}
