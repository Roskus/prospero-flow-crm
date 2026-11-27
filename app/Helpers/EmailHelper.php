<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Str;

class EmailHelper
{
    /**
     * Trim and lowercase an email. Accented letters of the local part are replaced with their
     * unaccented equivalent ("Damián.Muñoz@x.com" becomes "damian.munoz@x.com"). The domain keeps
     * its identity: a Unicode domain is converted to its IDNA ASCII form ("bücher.de" becomes
     * "xn--bcher-kva.de"), never transliterated, and is left as is when it cannot be converted.
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
        $domain = Str::lower($domain);

        if (Str::isAscii($domain)) {
            return $domain;
        }

        $asciiDomain = idn_to_ascii($domain, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);

        return $asciiDomain === false ? $domain : $asciiDomain;
    }
}
