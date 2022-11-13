<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory;
    //use SoftDeletes;

    protected $table = 'campaign';

    public function getAllByCompany(int $campaign_id)
    {
        return Campaign::where('company_id', $campaign_id)->get();
    }
}
