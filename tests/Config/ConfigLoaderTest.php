<?php

namespace Selfphp\SecretsDetector\Tests\Config;

use PHPUnit\Framework\TestCase;
use Selfphp\SecretsDetector\Config\ConfigLoader;

class ConfigLoaderTest extends TestCase
{
    private string $configFile;

    protected function setUp(): void
    {
        $this->configFile = __DIR__ . '/.test-secrets-detector.json';
        file_put_contents($this->configFile, json_encode([
            'include' => ['src', '.env'],
            'exclude' => ['vendor', 'node_modules']
        ]));
    }

    protected function tearDown(): void
    {
        unlink($this->configFile);
    }

    public function testLoadsIncludePaths(): void
    {
        $loader = new ConfigLoader($this->configFile);
        $this->assertSame(['src', '.env'], $loader->getIncludedPaths());
    }

    public function testLoadsExcludePaths(): void
    {
        $loader = new ConfigLoader($this->configFile);
        $this->assertSame(['vendor', 'node_modules'], $loader->getExcludedPaths());
    }

    public function testThrowsExceptionIfFileMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        new ConfigLoader('nonexistent.json');
    }

    public function testThrowsExceptionOnInvalidJson(): void
    {
        file_put_contents($this->configFile, '{invalid json');
        $this->expectException(\RuntimeException::class);
        new ConfigLoader($this->configFile);
    }
}
