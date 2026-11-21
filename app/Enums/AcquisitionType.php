<?php

declare(strict_types=1);

namespace App\Enums;

enum AcquisitionType: string
{
    case Purchase = 'purchase';
    case Renting = 'renting';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => __('Purchase'),
            self::Renting => __('Renting'),
        };
    }
}
