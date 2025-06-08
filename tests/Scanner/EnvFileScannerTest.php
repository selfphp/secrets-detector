<?php

namespace Selfphp\SecretsDetector\Tests\Scanner;

use PHPUnit\Framework\TestCase;
use Selfphp\SecretsDetector\Scanner\EnvFileScanner;

class EnvFileScannerTest extends TestCase
{
    private string $envPath;

    protected function setUp(): void
    {
        $this->envPath = __DIR__ . '/.env.test';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->envPath)) {
            unlink($this->envPath);
        }
    }

    public function testDetectsSuspiciousEnvEntry(): void
    {
        file_put_contents($this->envPath, "API_KEY=sk_test_123456\nDEBUG=true");
        $scanner = new EnvFileScanner();
        $results = $scanner->scanFile($this->envPath);

        $this->assertNotEmpty($results);
        $this->assertStringContainsString('API_KEY=sk_test_123456', $results[0]['content']);
    }

    public function testIgnoresHarmlessEnv(): void
    {
        file_put_contents($this->envPath, "DEBUG=true\nAPP_ENV=dev");
        $scanner = new EnvFileScanner();
        $results = $scanner->scanFile($this->envPath);

        $this->assertEmpty($results);
    }
}
