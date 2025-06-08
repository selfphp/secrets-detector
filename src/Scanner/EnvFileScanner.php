<?php

namespace Selfphp\SecretsDetector\Scanner;

use Selfphp\SecretsDetector\Pattern\PatternRegistry;

class EnvFileScanner
{
    /**
     * @param string $path Path to the file to scan (.env etc.)
     * @return array<int, array{line: int, content: string}>
     */
    public function scanFile(string $path): array
    {
        $matches = [];

        if (!is_file($path)) {
            return $matches;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return $matches;
        }

        foreach ($lines as $i => $line) {
            foreach (PatternRegistry::getEnvPatterns() as $pattern) {
                if (preg_match($pattern, $line)) {
                    $matches[] = [
                        'line' => $i + 1,
                        'content' => $line,
                    ];
                    break;
                }
            }
        }

        return $matches;
    }
}
