<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Str;

class EmailHelper
{
    /**
     * Trim and lowercase an email. Accented letters of the local part are replaced with their
     * unaccented equivalent ("Damián.Muñoz@x.com" becomes "damian.munoz@x.com"). The domain is only
     * lowercased, never transliterated, so a Unicode domain keeps its identity ("Bücher.de" stays
     * "bücher.de"); converting it to IDNA is left to the mail sender.
     */
    public static function normalize(string $email): string
    {
        $email = trim($email);
        $atPosition = mb_strrpos($email, '@');

        if ($atPosition === false) {
            return self::normalizeLocalPart($email);
        }

        $localPart = mb_substr($email, 0, $atPosition);
        $domain = mb_substr($email, $atPosition + 1);

        return self::normalizeLocalPart($localPart).'@'.self::normalizeDomain($domain);
    }

    private static function normalizeLocalPart(string $localPart): string
    {
        return Str::lower(Str::ascii($localPart));
    }

    private static function normalizeDomain(string $domain): string
    {
        return Str::lower($domain);
    }
}
