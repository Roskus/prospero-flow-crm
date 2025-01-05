<?php

declare(strict_types=1);

namespace App\Helpers;

class Domain
{
    public static function isValid(string $url): bool
    {
        $validation = false;
        /* Parse URL */
        $urlparts = parse_url(filter_var($url, FILTER_SANITIZE_URL));
        /* Check host exist else path assign to host */
        if (! isset($urlparts['host'])) {
            $urlparts['host'] = $urlparts['path'];
        }

        if ($urlparts['host'] != '') {
            /* Add scheme if not found */
            if (! isset($urlparts['scheme'])) {
                $urlparts['scheme'] = 'http';
            }
            /* Validation */
            if (checkdnsrr($urlparts['host'], 'A')
                && in_array($urlparts['scheme'], ['http', 'https'])
                && ip2long($urlparts['host']) === false) {
                $urlparts['host'] = preg_replace('/^www\./', '', $urlparts['host']);
                $url = $urlparts['scheme'].'://'.$urlparts['host'].'/';

                if (filter_var($url, FILTER_VALIDATE_URL) !== false && @get_headers($url)) {
                    $validation = true;
                }
            }
        }

        return $validation;
    }
}
