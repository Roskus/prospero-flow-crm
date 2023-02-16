<?php

declare(strict_types=1);

namespace App\Helpers;

class PhoneHelper
{
    public static function format(string $phone): string
    {
        $length = strlen($phone);
        $country_code = substr($phone, 0, 3);

        return match ($length) {
            11 => $country_code.'-'.substr($phone, 3, 4).'-'.substr($phone, 7, 4),
            12 => $country_code.'-'.substr($phone, 3, 3).'-'.substr($phone, 6, 3)
                .'-'.substr($phone, 9, 3),
            default => $phone,
        };
    }
}
