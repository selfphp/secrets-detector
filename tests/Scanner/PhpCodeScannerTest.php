<?php

namespace Selfphp\SecretsDetector\Tests\Scanner;

use PHPUnit\Framework\TestCase;
use Selfphp\SecretsDetector\Scanner\PhpCodeScanner;

class PhpCodeScannerTest extends TestCase
{
    private string $phpPath;

    protected function setUp(): void
    {
        $this->phpPath = __DIR__ . '/secret-test.php';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->phpPath)) {
            unlink($this->phpPath);
        }
    }

    public function testDetectsHardCodedSecretsOld(): void
    {
        $code = <<<'PHP'
<?php
$client = new Client("sk_live_abc123");
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer xyz456']);
PHP;

        file_put_contents($this->phpPath, $code);
        $scanner = new PhpCodeScanner();
        $results = $scanner->scan($this->phpPath);

        $this->assertCount(2, $results);
        $this->assertStringContainsString('sk_live_abc123', implode("\n", $results));
        $this->assertStringContainsString('Bearer xyz456', implode("\n", $results));
    }

    public function testIgnoresBenignCode(): void
    {
        $code = <<<'PHP'
<?php
echo "Hello World";
\$value = 42;
PHP;

        file_put_contents($this->phpPath, $code);
        $scanner = new PhpCodeScanner();
        $results = $scanner->scan($this->phpPath);

        $this->assertEmpty($results);
    }
}
