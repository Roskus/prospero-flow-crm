<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    const ACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'category';

    public function getAll()
    {
        return Category::orderBy('name', 'asc')->get();
    }

    /**
     * @param  int  $company_id
     * @return mixed
     */
    public function getAllActiveByCompany(int $company_id)
    {
        return Category::where('company_id', $company_id)
                        ->orderBy('name', 'asc')
                        ->get();
    }
}
