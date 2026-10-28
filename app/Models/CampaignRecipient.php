<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CampaignRecipientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One email sent by a campaign; reached through its campaign, which carries the company.
 */
class CampaignRecipient extends Model
{
    /** @use HasFactory<CampaignRecipientFactory> */
    use HasFactory;

    public const string STATUS_SENT = 'sent';

    public const string STATUS_BOUNCED = 'bounced';

    protected $table = 'campaign_recipient';

    protected $fillable = [
        'campaign_id',
        'lead_id',
        'email',
        'status',
        'error',
        'sent_at',
        'opened_at',
        'open_count',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'opened_at' => 'datetime',
            'open_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
