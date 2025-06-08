<?php

namespace Selfphp\SecretsDetector\Pattern;

class PatternRegistry
{
    /**
     * Returns all regular expressions for scanning PHP code.
     *
     * @return array<int, string>
     */
    public static function getPhpPatterns(): array
    {
        return [
            '/sk_(live|test)_[0-9a-zA-Z]{6,}/i',
            '/Bearer\s+[a-zA-Z0-9\-_\.]{6,}/i',
            '/Authorization\s*[:=]\s*[\'"]?Bearer\s+[a-zA-Z0-9\-_\.]{6,}/i',
            '/Client\s*\(\s*[\'"]sk_(live|test)_[0-9a-zA-Z]{6,}/i',
            '/AWS_SECRET_ACCESS_KEY\s*=\s*[\'"]?[a-zA-Z0-9\/+=]{20,}/i',
            '/AWS_ACCESS_KEY_ID\s*=\s*[\'"]?[A-Z0-9]{16,}/i',
            '/JWT_SECRET\s*=\s*[\'"]?[a-zA-Z0-9\-_\.]{10,}/i',
            '/APP_SECRET\s*=\s*[\'"]?[a-zA-Z0-9]{10,}/i',
            '/DB_PASSWORD\s*=\s*[\'"]?.{6,}/i',
            '/DB_USERNAME\s*=\s*[\'"]?.{3,}/i',
            '/AIza[0-9A-Za-z\-_]{10,}/',
            '/-----BEGIN(.*?)PRIVATE KEY-----/s',
            '/key\s*=\s*[\'"]?[a-zA-Z0-9\-_]{6,}/i',
            '/token\s*=\s*[\'"]?[a-zA-Z0-9\-_]{6,}/i',
        ];
    }

    /**
     * Returns regular expressions for scanning .env files.
     *
     * @return array<int, string>
     */
    public static function getEnvPatterns(): array
    {
        return [
            '/^\s*APP_SECRET\s*=\s*.{8,}/i',
            '/^\s*JWT_SECRET\s*=\s*.{8,}/i',
            '/^\s*DB_PASSWORD\s*=\s*.{6,}/i',
            '/^\s*API_KEY\s*=\s*.{8,}/i',
            '/^\s*AWS_SECRET_ACCESS_KEY\s*=\s*.{16,}/i',
        ];
    }

    /**
     * Returns regular expressions for scanning .ini or config files.
     *
     * @return array<int, string>
     */
    public static function getIniPatterns(): array
    {
        return [
            '/^\s*password\s*=\s*.{6,}/i',
            '/^\s*secret\s*=\s*.{6,}/i',
            '/^\s*api[_-]?key\s*=\s*.{6,}/i',
        ];
    }
}
