<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;
    protected $table = 'industry';

    public function getAll()
    {
        //@TODO Add to the cache
        return Industry::all();
    }
}
