<?php

declare(strict_types=1);

namespace App\Enums;

enum AssetCategory: string
{
    case Vehicle = 'vehicle';
    case Device = 'device';

    public function label(): string
    {
        return match ($this) {
            self::Vehicle => __('Vehicles & machinery'),
            self::Device => __('Devices'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Vehicle => 'las la-truck',
            self::Device => 'las la-laptop',
        };
    }
}
