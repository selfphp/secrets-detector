<?php

namespace Selfphp\SecretsDetector\Command;

use Selfphp\SecretsDetector\Config\ConfigLoader;
use Selfphp\SecretsDetector\Scanner\EnvFileScanner;
use Selfphp\SecretsDetector\Scanner\PhpCodeScanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScanCommand extends Command
{
    public function __construct()
    {
        parent::__construct('secrets:scan');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Scans the project for potential secrets in code and config files.')
            ->setHelp('This command scans .env, PHP source files and config files for potential secrets like API keys or passwords.')
            ->addOption('json', null, InputOption::VALUE_REQUIRED, 'Path to write results as JSON')
            ->addOption('markdown', null, InputOption::VALUE_REQUIRED, 'Path to write results as Markdown')
            ->addOption('fail-on-detect', null, InputOption::VALUE_NONE, 'Exit with error code if secrets are detected');
    }

    private function isIgnored(string $filePath, array $ignoreList): bool
    {
        foreach ($ignoreList as $ignored) {
            // Pfade normalisieren und auf Prefix vergleichen
            if (str_starts_with(str_replace('\\', '/', $filePath), rtrim($ignored, '/'))) {
                return true;
            }
        }

        return false;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🔍 Scanning project for secrets...</info>');

        $config = new ConfigLoader();
        $envScanner = new EnvFileScanner();
        $phpScanner = new PhpCodeScanner();

        $included = $config->getIncludedPaths();
        $excluded = array_map(fn($e) => realpath($e), $config->getExcludedPaths());

        $findings = [];

        foreach ($included as $path) {
            if (!file_exists($path)) {
                continue;
            }

            $realPath = realpath($path);
            if (is_dir($realPath)) {
                $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($realPath));
                foreach ($rii as $file) {
                    if ($file->isDir()) {
                        continue;
                    }

                    $filePath = $file->getPathname();

                    foreach ($excluded as $ex) {
                        if ($ex && str_starts_with($filePath, $ex)) {
                            continue 2;
                        }
                    }

                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    if ($extension === 'php') {
                        $results = $phpScanner->scan($filePath);
                        if ($results) {
                            $findings[$filePath] = $results;
                        }
                    }
                }
            } elseif (pathinfo($realPath, PATHINFO_EXTENSION) === 'env') {
                $results = $envScanner->scan($realPath);
                if ($results) {
                    $findings[$realPath] = $results;
                }
            }
        }

        if ($input->getOption('json')) {
            file_put_contents($input->getOption('json'), json_encode($findings, JSON_PRETTY_PRINT));
            $output->writeln('<info>📄 JSON report written to:</info> ' . $input->getOption('json'));
        }

        if ($input->getOption('markdown')) {
            $md = "# Secrets Detector Report\n\n";
            foreach ($findings as $file => $lines) {
                $md .= "## {$file}\n";
                foreach ($lines as $line) {
                    $md .= "- `{$line}`\n";
                }
                $md .= "\n";
            }
            file_put_contents($input->getOption('markdown'), $md);
            $output->writeln('<info>📝 Markdown report written to:</info> ' . $input->getOption('markdown'));
        }

        if (empty($findings)) {
            $output->writeln('<info>✅ No secrets detected.</info>');
            return Command::SUCCESS;
        }

        foreach ($findings as $file => $lines) {
            $output->writeln("<error>🚨 {$file}</error>");
            foreach ($lines as $line) {
                $output->writeln("  <fg=yellow>» {$line}</>");
            }
        }

        return $input->getOption('fail-on-detect') ? Command::FAILURE : Command::SUCCESS;
    }
}
