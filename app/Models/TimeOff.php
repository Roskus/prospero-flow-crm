<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeOff extends Model
{
    use HasFactory;

    protected $table = 'time_off';

    protected $fillable = [
        'company_id',
        'user_id',
        'type',
        'start_date',
        'end_date',
        'days_used',
        'reason',
        'attachment',
        'status',
        'manager_approved_by',
        'manager_approved_at',
        'rrhh_approved_by',
        'rrhh_approved_at',
        'rejected_reason',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'manager_approved_at' => 'datetime',
        'rrhh_approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function managerApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    public function rrhhApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rrhh_approved_by');
    }
}
