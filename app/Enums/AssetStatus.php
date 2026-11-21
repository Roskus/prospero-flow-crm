<?php

declare(strict_types=1);

namespace App\Enums;

enum AssetStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case InMaintenance = 'in_maintenance';
    case Retired = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::Available => __('Available'),
            self::Assigned => __('Assigned'),
            self::InMaintenance => __('In maintenance'),
            self::Retired => __('Retired'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'text-bg-success',
            self::Assigned => 'text-bg-primary',
            self::InMaintenance => 'text-bg-warning',
            self::Retired => 'text-bg-secondary',
        };
    }
}
