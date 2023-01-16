<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  @OA\Schema(
 *    schema="Campaign",
 *    type="object",
 *    required={"subject", "body"},
 *  )
 */
class Campaign extends Model
{
    use HasFactory;
    //use SoftDeletes;

    protected $table = 'campaign';

    protected $fillable =
        [
            'company_id',
            'subject',
            'body',
            'schedule_send_date',
            'schedule_send_time',
            'send_at',
            'emails_count',
        ];

    public function getAllByCompany(int $campaign_id)
    {
        return Campaign::where('company_id', $campaign_id)->get();
    }
}
