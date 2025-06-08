<?php

namespace Selfphp\SecretsDetector\Config;

class ConfigLoader
{
    /**
     * @var string
     */
    private string $configPath;

    /**
     * @var array<string>
     */
    private array $include = [];

    /**
     * @var array<string>
     */
    private array $exclude = [];

    public function __construct(string $configPath = '.secrets-detector.json')
    {
        $this->configPath = $configPath;
        $this->load();
    }

    private function load(): void
    {
        if (!file_exists($this->configPath)) {
            throw new \RuntimeException("Config file not found: {$this->configPath}");
        }

        $data = json_decode(file_get_contents($this->configPath), true);
        if (!is_array($data)) {
            throw new \RuntimeException("Invalid config format in {$this->configPath}");
        }

        $this->include = array_map('trim', $data['include'] ?? []);
        $this->exclude = array_merge(
            array_map('trim', $data['exclude'] ?? []),
            array_map('trim', $data['ignore'] ?? [])
        );
    }

    public function getIncludedPaths(): array
    {
        return $this->include;
    }

    public function getExcludedPaths(): array
    {
        return $this->exclude;
    }
}
