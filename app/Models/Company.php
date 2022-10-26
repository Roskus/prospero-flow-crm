<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Squire\Models\Country;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    const ACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company';
    protected $fillable = [
        'name',
        'logo',
        'phone',
        'email',
        'country_id',
        'website',
        'status',
    ];

    public function getAll()
    {
        return Company::orderBy('name', 'asc')->get();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
