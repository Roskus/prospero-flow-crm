<?php

declare(strict_types=1);

namespace App\Enums;

enum WeightUnit: string
{
    case Gram = 'g';
    case Kilogram = 'kg';
    case Pound = 'lb';
    case Ounce = 'oz';
}
