<?php

declare(strict_types=1);

namespace App\Helpers;

class PhoneHelper
{
    public static function format(string $phone): string
    {
        $length = strlen($phone);
        $country_code = substr($phone, 0, 3);
        switch ($length) {
            case 11:
                $format = $country_code.'-'.substr($phone, 3, 4).'-'.substr($phone, 7, 4);
                break;
            case 12:
                $format = $country_code.'-'.substr($phone, 3, 3).'-'.substr($phone, 6, 3).'-'.substr($phone, 9, 3);
                break;
            default:
                $format = $phone;
        }

        return $format;
    }
}
