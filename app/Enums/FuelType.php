<?php

declare(strict_types=1);

namespace App\Enums;

enum FuelType: string
{
    case Gasoline = 'gasoline';
    case Diesel = 'diesel';
    case Electric = 'electric';
    case Hybrid = 'hybrid';
    case Lpg = 'lpg';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Gasoline => __('Gasoline'),
            self::Diesel => __('Diesel'),
            self::Electric => __('Electric'),
            self::Hybrid => __('Hybrid'),
            self::Lpg => __('LPG'),
            self::Other => __('Other'),
        };
    }
}
