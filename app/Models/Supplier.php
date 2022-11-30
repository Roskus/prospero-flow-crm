<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Squire\Models\Country;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getAllByCompany(int $company_id)
    {
        return Supplier::where('company_id', $company_id)->get();
    }
}
