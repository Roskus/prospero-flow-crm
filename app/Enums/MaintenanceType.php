<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceType: string
{
    case Preventive = 'preventive';
    case Corrective = 'corrective';
    case Inspection = 'inspection';

    public function label(): string
    {
        return match ($this) {
            self::Preventive => __('Preventive'),
            self::Corrective => __('Corrective'),
            self::Inspection => __('Inspection'),
        };
    }
}
