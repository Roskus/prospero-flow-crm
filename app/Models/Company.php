<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Squire\Models\Country;

class Company extends Model
{
    use SoftDeletes;

    const ACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company';

    public function getAll()
    {
        return Company::orderBy('name', 'asc')->get();
    }

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id', 'id');
    }
}
