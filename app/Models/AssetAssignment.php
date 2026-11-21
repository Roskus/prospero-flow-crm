<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OAT;

/**
 * @property int $id
 * @property int $asset_id
 * @property int $user_id
 * @property ?int $assigned_by
 * @property Carbon $start_date
 * @property ?Carbon $end_date
 * @property ?string $notes
 * @property ?User $user
 * @property ?User $assignedBy
 */
#[OAT\Schema(
    schema: 'AssetAssignment',
    required: ['user_id', 'start_date'],
    properties: [
        new OAT\Property(property: 'id', description: 'Assignment ID', type: 'integer', example: 1),
        new OAT\Property(property: 'asset_id', description: 'Asset ID', type: 'integer', example: 1),
        new OAT\Property(property: 'user_id', description: 'User holding the asset', type: 'integer', example: 3),
        new OAT\Property(property: 'start_date', description: 'Assignment start date', type: 'string', format: 'date', example: '2026-11-01'),
        new OAT\Property(property: 'end_date', description: 'Return date, null while assigned', type: 'string', format: 'date', example: null),
        new OAT\Property(property: 'notes', description: 'Notes', type: 'string'),
    ],
    type: 'object',
)]
class AssetAssignment extends Model
{
    use HasFactory;

    protected $table = 'asset_assignment';

    protected $fillable = [
        'asset_id',
        'user_id',
        'assigned_by',
        'start_date',
        'end_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Deleted users keep showing in the assignment history.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by')->withTrashed();
    }

    public function isOpen(): bool
    {
        return $this->end_date === null;
    }
}
