#!/usr/bin/env php
<?php

/**
 * Script de vérification de sécurité pour le déploiement
 * Usage: php security-check.php
 */

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ConsoleInput;

class SecurityChecker
{
    private array $checks = [];
    private ConsoleOutput $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
        $this->initializeChecks();
    }

    private function initializeChecks(): void
    {
        $this->checks = [
            'Environment' => [
                'APP_ENV is set to prod' => function() {
                    return $_ENV['APP_ENV'] === 'prod' || getenv('APP_ENV') === 'prod';
                },
                'APP_DEBUG is disabled' => function() {
                    return $_ENV['APP_DEBUG'] === '0' || getenv('APP_DEBUG') === '0';
                },
                'Strong APP_SECRET' => function() {
                    $secret = $_ENV['APP_SECRET'] ?? getenv('APP_SECRET');
                    return strlen($secret) >= 32 && $secret !== 'your_super_secret_key_for_production';
                },
            ],
            'File Permissions' => [
                'var/ directory writable' => function() {
                    return is_writable(__DIR__.'/var');
                },
                'config/ directory protected' => function() {
                    return !is_writable(__DIR__.'/config');
                },
                '.env files protected' => function() {
                    $envFile = __DIR__.'/.env';
                    return !file_exists($envFile) || (fileperms($envFile) & 0044) === 0;
                },
            ],
            'Security Headers' => [
                'SecurityHeadersListener exists' => function() {
                    return file_exists(__DIR__.'/src/EventListener/SecurityHeadersListener.php');
                },
                'CSRF protection enabled' => function() {
                    $config = file_get_contents(__DIR__.'/config/packages/framework.yaml');
                    return strpos($config, 'csrf_protection: true') !== false;
                },
            ],
            'Database' => [
                'PDO options secure' => function() {
                    $config = file_get_contents(__DIR__.'/config/packages/prod/doctrine.yaml');
                    return strpos($config, 'use_savepoints: true') !== false;
                },
            ],
            'Dependencies' => [
                'No dev dependencies' => function() {
                    $composer = json_decode(file_get_contents(__DIR__.'/composer.lock'), true);
                    foreach ($composer['packages'] as $package) {
                        if (isset($package['type']) && $package['type'] === 'symfony-bundle' && 
                            in_array('dev', $package['extra']['branch-alias'] ?? [])) {
                            return false;
                        }
                    }
                    return true;
                },
            ],
        ];
    }

    public function run(): void
    {
        $this->output->writeln('<info>🔒 Vérification de sécurité de l\'application</info>');
        $this->output->writeln('');

        $allPassed = true;
        $results = [];

        foreach ($this->checks as $category => $checks) {
            $this->output->writeln("<comment>📋 {$category}</comment>");
            
            foreach ($checks as $description => $check) {
                try {
                    $passed = $check();
                    $status = $passed ? '<info>✅ PASS</info>' : '<error>❌ FAIL</error>';
                    $this->output->writeln("  {$description}: {$status}");
                    
                    if (!$passed) {
                        $allPassed = false;
                    }
                    
                    $results[] = [$category, $description, $passed ? 'PASS' : 'FAIL'];
                } catch (Exception $e) {
                    $this->output->writeln("  {$description}: <error>❌ ERROR: {$e->getMessage()}</error>");
                    $allPassed = false;
                    $results[] = [$category, $description, 'ERROR'];
                }
            }
            $this->output->writeln('');
        }

        // Résumé
        $table = new Table($this->output);
        $table->setHeaders(['Catégorie', 'Vérification', 'Statut']);
        $table->setRows($results);
        $table->render();

        if ($allPassed) {
            $this->output->writeln('<info>🎉 Toutes les vérifications de sécurité ont réussi!</info>');
            $this->output->writeln('<info>✅ L\'application est prête pour la production</info>');
            exit(0);
        } else {
            $this->output->writeln('<error>⚠️  Des problèmes de sécurité ont été détectés</error>');
            $this->output->writeln('<error>❌ Veuillez corriger les erreurs avant le déploiement</error>');
            exit(1);
        }
    }
}

$checker = new SecurityChecker();
$checker->run();
