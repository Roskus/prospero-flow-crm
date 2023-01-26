<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

//use Illuminate\Database\Eloquent\SoftDeletes;

#[OAT\Schema(schema: 'Campaign', required: ['subject', 'body'], type: 'object')]
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

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id;

    #[OAT\Property(type: 'string', example: 'My Amazing campaign')]
    protected string $subject;

    #[OAT\Property(type: 'string', example: 'My awesome campaign content')]
    protected string $body;

    public function getAllByCompany(int $campaign_id)
    {
        return Campaign::where('company_id', $campaign_id)->get();
    }
}
