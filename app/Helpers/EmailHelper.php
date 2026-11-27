<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Str;

class EmailHelper
{
    /**
     * Trim, lowercase and replace accented letters with their unaccented equivalent
     * (e.g. "Damián.Muñoz@Example.com" becomes "damian.munoz@example.com").
     */
    public static function normalize(string $email): string
    {
        return Str::lower(Str::ascii(trim($email)));
    }
}
