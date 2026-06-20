<?php

declare(strict_types=1);

namespace App\Helpers;

class Domain
{
    public static function isValid(string $url): bool
    {
        $validation = false;
        $urlparts = parse_url(filter_var($url, FILTER_SANITIZE_URL));

        if (! isset($urlparts['host'])) {
            $urlparts['host'] = $urlparts['path'] ?? '';
        }

        if ($urlparts['host'] === '') {
            return false;
        }

        if (! isset($urlparts['scheme'])) {
            $urlparts['scheme'] = 'http';
        }

        if (! in_array($urlparts['scheme'], ['http', 'https'], true)) {
            return false;
        }

        // Reject explicit IP addresses
        if (self::isPrivateIp($urlparts['host'])) {
            return false;
        }

        // Resolve hostname and validate resulting IP is public
        $ips = @gethostbyname($urlparts['host']);
        if ($ips === false || $ips === $urlparts['host']) {
            return false;
        }

        foreach ((array) $ips as $ip) {
            if (self::isPrivateIp($ip)) {
                return false;
            }
        }

        $urlparts['host'] = preg_replace('/^www\./', '', $urlparts['host']);
        $url = $urlparts['scheme'].'://'.$urlparts['host'].'/';

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RESOLVE_ONLY_PUBLIC_IPS => true,
        ]);

        $result = @curl_exec($ch);
        curl_close($ch);

        return $result !== false;
    }

    private static function isPrivateIp(string $ip): bool
    {
        $long = ip2long($ip);
        if ($long === false) {
            return true;
        }

        return (
            ($long >= ip2long('127.0.0.0') && $long <= ip2long('127.255.255.255')) ||
            ($long >= ip2long('10.0.0.0') && $long <= ip2long('10.255.255.255')) ||
            ($long >= ip2long('172.16.0.0') && $long <= ip2long('172.31.255.255')) ||
            ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255')) ||
            ($long >= ip2long('169.254.0.0') && $long <= ip2long('169.254.255.255')) ||
            ($long >= ip2long('127.0.0.0') && $long <= ip2long('127.255.255.255'))
        );
    }
}
