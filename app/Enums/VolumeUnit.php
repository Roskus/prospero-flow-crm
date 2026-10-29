<?php

declare(strict_types=1);

namespace App\Enums;

enum VolumeUnit: string
{
    case Milliliter = 'ml';
    case Centiliter = 'cl';
    case Liter = 'l';
    case FluidOunce = 'fl oz';
}
