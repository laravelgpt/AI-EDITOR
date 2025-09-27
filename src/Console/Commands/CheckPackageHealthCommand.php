<?php

namespace AiEditor\AiTextEditor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckPackageHealthCommand extends Command
{
    protected $signature = 'ai-editor:check-health';
    protected $description = 'Check AI Text Editor package health and fix common issues';

    public function handle(): int
    {
        $this->info('🔍 AI Text Editor - Package Health Check');
        $this->info('=====================================');
        $this->newLine();

        $issues = [];
        $fixes = [];

        // Check configuration file
        $this->checkConfigurationFile($issues, $fixes);

        // Check service provider
        $this->checkServiceProvider($issues, $fixes);

        // Check Livewire component
        $this->checkLivewireComponent($issues, $fixes);

        // Check migrations
        $this->checkMigrations($issues, $fixes);

        // Check views
        $this->checkViews($issues, $fixes);

        // Display results
        if (empty($issues)) {
            $this->info('✅ All checks passed! Package is healthy.');
            return 0;
        }

        $this->warn('⚠️  Found ' . count($issues) . ' issues:');
        $this->newLine();

        foreach ($issues as $issue) {
            $this->error("❌ {$issue}");
        }

        if (!empty($fixes)) {
            $this->newLine();
            $this->info('🔧 Suggested fixes:');
            foreach ($fixes as $fix) {
                $this->line("• {$fix}");
            }
        }

        return 1;
    }

    protected function checkConfigurationFile(array &$issues, array &$fixes): void
    {
        $configPath = config_path('ai-text-editor.php');
        
        if (!File::exists($configPath)) {
            $issues[] = 'Configuration file not found';
            $fixes[] = 'Run: php artisan vendor:publish --provider="AiEditor\\AiTextEditor\\AiTextEditorServiceProvider" --tag="config"';
            return;
        }

        // Check for syntax errors
        $output = [];
        $returnCode = 0;
        exec("php -l \"{$configPath}\" 2>&1", $output, $returnCode);

        if ($returnCode !== 0) {
            $issues[] = 'Configuration file has syntax errors: ' . implode(' ', $output);
            $fixes[] = 'Check the configuration file for syntax errors and fix them';
        }
    }

    protected function checkServiceProvider(array &$issues, array &$fixes): void
    {
        $providerPath = __DIR__ . '/../AiTextEditorServiceProvider.php';
        
        if (!File::exists($providerPath)) {
            $issues[] = 'Service provider not found';
            return;
        }

        // Check for syntax errors
        $output = [];
        $returnCode = 0;
        exec("php -l \"{$providerPath}\" 2>&1", $output, $returnCode);

        if ($returnCode !== 0) {
            $issues[] = 'Service provider has syntax errors: ' . implode(' ', $output);
            $fixes[] = 'Check the service provider for syntax errors and fix them';
        }
    }

    protected function checkLivewireComponent(array &$issues, array &$fixes): void
    {
        $componentPath = __DIR__ . '/../Livewire/AiTextEditor.php';
        
        if (!File::exists($componentPath)) {
            $issues[] = 'Livewire component not found';
            return;
        }

        // Check for syntax errors
        $output = [];
        $returnCode = 0;
        exec("php -l \"{$componentPath}\" 2>&1", $output, $returnCode);

        if ($returnCode !== 0) {
            $issues[] = 'Livewire component has syntax errors: ' . implode(' ', $output);
            $fixes[] = 'Check the Livewire component for syntax errors and fix them';
        }
    }

    protected function checkMigrations(array &$issues, array &$fixes): void
    {
        $migrationPath = __DIR__ . '/../../database/migrations';
        
        if (!File::exists($migrationPath)) {
            $issues[] = 'Migrations directory not found';
            return;
        }

        $migrations = File::glob($migrationPath . '/*.php');
        
        if (empty($migrations)) {
            $issues[] = 'No migration files found';
            $fixes[] = 'Ensure migration files are present in the package';
        }
    }

    protected function checkViews(array &$issues, array &$fixes): void
    {
        $viewPath = __DIR__ . '/../../resources/views';
        
        if (!File::exists($viewPath)) {
            $issues[] = 'Views directory not found';
            return;
        }

        $views = File::glob($viewPath . '/**/*.blade.php');
        
        if (empty($views)) {
            $issues[] = 'No view files found';
            $fixes[] = 'Ensure view files are present in the package';
        }
    }
}
