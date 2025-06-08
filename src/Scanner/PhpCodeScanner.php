<?php

namespace Selfphp\SecretsDetector\Scanner;

use Selfphp\SecretsDetector\Pattern\PatternRegistry;

class PhpCodeScanner
{
    /**
     * Scans a PHP file for hardcoded secrets like API keys or tokens.
     *
     * @param string $filePath
     * @return array<int, string>
     */
    public function scan(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $suspicious = [];

        foreach ($lines as $line) {
            foreach (PatternRegistry::getPhpPatterns() as $pattern) {
                if (preg_match($pattern, $line)) {
                    $suspicious[] = $line;
                    break;
                }
            }
        }

        return $suspicious;
    }
}
