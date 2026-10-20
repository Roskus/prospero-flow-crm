<?php

declare(strict_types=1);

namespace App\Helpers;

class Domain
{
    /**
     * Check that the website is reachable, refusing IP literals and hosts that resolve to non-public IPs (SSRF).
     */
    public static function isValid(string $url): bool
    {
        $urlparts = parse_url(filter_var($url, FILTER_SANITIZE_URL));

        if ($urlparts === false) {
            return false;
        }

        if (! isset($urlparts['host'])) {
            $urlparts['host'] = $urlparts['path'] ?? '';
        }

        if ($urlparts['host'] === '') {
            return false;
        }

        if (! isset($urlparts['scheme'])) {
            $urlparts['scheme'] = 'http';
        }

        $scheme = strtolower($urlparts['scheme']);
        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = preg_replace('/^www\./', '', strtolower($urlparts['host']));
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return false;
        }

        $url = $scheme.'://'.$host.'/';
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $ips = self::resolvePublicIps($host);
        if ($ips === []) {
            return false;
        }

        $port = $scheme === 'https' ? 443 : 80;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_RESOLVE => ["{$host}:{$port}:{$ips[0]}"],
        ]);
        $result = @curl_exec($ch);
        curl_close($ch);

        return $result !== false;
    }

    /**
     * Resolve the host IPv4 addresses, returning none if any of them is private or reserved.
     *
     * @return list<string>
     */
    private static function resolvePublicIps(string $host): array
    {
        $ips = @gethostbynamel($host);
        if ($ips === false || $ips === []) {
            return [];
        }

        foreach ($ips as $ip) {
            if (self::isPrivateIp($ip)) {
                return [];
            }
        }

        return array_values($ips);
    }

    private static function isPrivateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
