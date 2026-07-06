<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Campaign',
    required: ['subject', 'from', 'body', 'tags'],
    properties: [
        new OAT\Property(property: 'id', description: 'Campaign ID', type: 'integer', example: 1),
        new OAT\Property(property: 'subject', description: 'Campaign subject', type: 'string', example: 'My amazing campaign'),
        new OAT\Property(property: 'from', description: 'From email address', type: 'string', format: 'email', example: 'origin@mail.com'),
        new OAT\Property(property: 'body', description: 'Campaign body content', type: 'string', example: 'My awesome campaign content'),
        new OAT\Property(property: 'tags', description: 'Campaign tags', type: 'string', example: 'Tags'),
        new OAT\Property(property: 'schedule_send_date', description: 'Scheduled send date', type: 'string', format: 'date', example: '2023-01-10'),
        new OAT\Property(property: 'schedule_send_time', description: 'Scheduled send time', type: 'string', format: 'time', example: '16:15:00'),
        new OAT\Property(property: 'send_at', description: 'Sent at date', type: 'string', format: 'date', example: '2023-01-10'),
        new OAT\Property(property: 'emails_count', description: 'Number of emails sent', type: 'integer', example: 100),
    ],
    type: 'object'
)]
class Campaign extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'campaign';

    protected $fillable = [
        'subject',
        'from',
        'body',
        'tags',
        'schedule_send_date',
        'schedule_send_time',
        'send_at',
        'emails_count',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function getAllByCompany(int $campaign_id)
    {
        return Campaign::where('company_id', $campaign_id)->get();
    }
}
