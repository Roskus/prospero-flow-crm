<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Campaign', required: ['subject', 'from', 'body', 'tags'], type: 'object')]
class Campaign extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'campaign';

    protected $fillable =
        [
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

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id; // NOSONAR

    private ?int $company_id; // NOSONAR

    #[OAT\Property(type: 'string', example: 'My amazing campaign')]
    protected string $subject;

    #[OAT\Property(type: 'string', example: 'origin@mail.com')]
    protected string $from;

    #[OAT\Property(type: 'string', example: 'My awesome campaign content')]
    protected string $body;

    #[OAT\Property(type: 'string', example: 'Tags')]
    protected string $tags;

    #[OAT\Property(type: 'string', format: 'date', example: '2023-01-10')]
    protected \DateTime $schedule_send_date;

    #[OAT\Property(type: 'string', format: 'time', example: '16:15:00')]
    protected \DateTime $schedule_send_time;

    #[OAT\Property(type: 'string', format: 'date', example: '2023-01-10')]
    protected ?\DateTime $send_at;

    #[OAT\Property(type: 'int', example: 100)]
    protected ?int $emails_count;

    public function getAllByCompany(int $campaign_id)
    {
        return Campaign::where('company_id', $campaign_id)->get();
    }
}
