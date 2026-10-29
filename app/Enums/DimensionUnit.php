<?php

declare(strict_types=1);

namespace App\Enums;

enum DimensionUnit: string
{
    case Millimeter = 'mm';
    case Centimeter = 'cm';
    case Meter = 'm';
    case Inch = 'in';
}
