<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Campaign',
    required: ['name', 'subject', 'from', 'body', 'tags'],
    properties: [
        new OAT\Property(property: 'id', description: 'Campaign ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Campaign name', type: 'string', example: 'Autumn newsletter'),
        new OAT\Property(property: 'subject', description: 'Campaign subject', type: 'string', example: 'My amazing campaign'),
        new OAT\Property(property: 'from', description: 'From email address', type: 'string', format: 'email', example: 'origin@mail.com'),
        new OAT\Property(property: 'body', description: 'Campaign body content', type: 'string', example: 'My awesome campaign content'),
        new OAT\Property(property: 'tags', description: 'Campaign tags', type: 'string', example: 'Tags'),
        new OAT\Property(property: 'schedule_send_date', description: 'Scheduled send date', type: 'string', format: 'date', example: '2023-01-10'),
        new OAT\Property(property: 'schedule_send_time', description: 'Scheduled send time', type: 'string', format: 'time', example: '16:15:00'),
        new OAT\Property(property: 'schedule_at', description: 'Scheduled send date and time in UTC', type: 'string', format: 'date-time', example: '2023-01-10 15:15:00'),
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
        'name',
        'subject',
        'from',
        'body',
        'tags',
        'schedule_send_date',
        'schedule_send_time',
        'schedule_at',
        'send_at',
        'emails_count',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'schedule_at' => 'datetime',
        'send_at' => 'datetime',
        'emails_count' => 'integer',
    ];

    /**
     * The moment, in the application time zone, of a date and time entered in the given time zone; without a time, the start of the day.
     */
    public static function scheduleMoment(?string $date, ?string $time, string $timezone): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        return Carbon::parse($date.' '.($time ?: '00:00'), $timezone)->setTimezone(config('app.timezone'));
    }

    /**
     * Recipient fields a campaign can use as {{ name }}; the older {{ $prospect->first_name }} form is still read for campaigns written with it.
     */
    private const array RECIPIENT_VARIABLES = [
        'first_name' => 'name',
        'name' => 'name',
        'business_name' => 'business_name',
    ];

    /**
     * Replaces the {{ name }} and {{ business_name }} variables with the values of the lead, escaped for HTML text; the text is never evaluated as Blade.
     */
    public static function personalize(?string $text, Lead $lead, bool $isHtml = true): string
    {
        return (string) preg_replace_callback(
            '/\{\{\s*(?:\$prospect\s*-(?:>|&gt;)\s*)?(\w+)\s*\}\}/',
            function (array $match) use ($lead, $isHtml): string {
                $field = self::RECIPIENT_VARIABLES[$match[1]] ?? null;
                if ($field === null) {
                    return $match[0];
                }
                $value = (string) $lead->getAttribute($field);

                return $isHtml ? e($value) : $value;
            },
            (string) $text,
        );
    }

    /**
     * Addresses a campaign can be sent from: the company and the user.
     *
     * @return list<array{email: string, name: string}>
     */
    public static function fromAddresses(User $user): array
    {
        return collect([
            ['email' => (string) $user->company?->email, 'name' => (string) $user->company?->name],
            ['email' => (string) $user->email, 'name' => trim($user->first_name.' '.$user->last_name)],
        ])->filter(fn (array $from): bool => $from['email'] !== '')->unique('email')->values()->all();
    }

    /**
     * @return HasMany<CampaignRecipient, $this>
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    /**
     * Results of the sent campaigns of a company, most recent first, with a totals row.
     *
     * @return array{rows: list<array{campaign: Campaign, sent: int, delivered: int, opened: int, bounced: int, open_rate: float, bounce_rate: float}>, totals: array{sent: int, delivered: int, opened: int, bounced: int, open_rate: float, bounce_rate: float}}
     */
    public static function getEmailReport(int $companyId): array
    {
        $campaigns = self::query()
            ->where('company_id', $companyId)
            ->whereNotNull('send_at')
            ->withCount([
                'recipients',
                'recipients as bounced_count' => fn ($query) => $query->where('status', CampaignRecipient::STATUS_BOUNCED),
                'recipients as opened_count' => fn ($query) => $query->whereNotNull('opened_at'),
            ])
            ->orderByDesc('send_at')
            ->orderByDesc('id')
            ->get();

        $rows = $campaigns->map(fn (Campaign $campaign): array => [
            'campaign' => $campaign,
            ...self::emailFigures(
                $campaign->recipients_count > 0 ? (int) $campaign->recipients_count : (int) $campaign->emails_count,
                (int) $campaign->getAttribute('opened_count'),
                (int) $campaign->getAttribute('bounced_count'),
            ),
        ])->values()->all();

        return [
            'rows' => $rows,
            'totals' => self::emailFigures(
                (int) array_sum(array_column($rows, 'sent')),
                (int) array_sum(array_column($rows, 'opened')),
                (int) array_sum(array_column($rows, 'bounced')),
            ),
        ];
    }

    /**
     * @return array{sent: int, delivered: int, opened: int, bounced: int, open_rate: float, bounce_rate: float}
     */
    private static function emailFigures(int $sent, int $opened, int $bounced): array
    {
        $delivered = max(0, $sent - $bounced);

        return [
            'sent' => $sent,
            'delivered' => $delivered,
            'opened' => $opened,
            'bounced' => $bounced,
            'open_rate' => self::percentage($opened, $delivered),
            'bounce_rate' => self::percentage($bounced, $sent),
        ];
    }

    private static function percentage(int $part, int $total): float
    {
        return $total > 0 ? round($part * 100 / $total, 1) : 0.0;
    }

    public function getAllByCompany(int $campaign_id)
    {
        return Campaign::where('company_id', $campaign_id)->get();
    }
}
