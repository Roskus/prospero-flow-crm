<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * @param  string  $phone
     * @return string
     */
    public static function format(string $phone): string
    {
        $length = strlen($phone);
        switch ($length) {
            case 12:
                $format = substr($phone, 0, 3).'-'.substr($phone, 3, 3).'-'.substr($phone, 6, 3).'-'.substr($phone, 9, 3);
                break;
            default:
                $format = $phone;
        }

        return $format;
    }
}
