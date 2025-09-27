<?php

namespace AiEditor\AiTextEditor\Console\Commands;

use Illuminate\Console\Command;
use AiEditor\AiTextEditor\Services\InstallerService;
use AiEditor\AiTextEditor\Services\StackService;
use AiEditor\AiTextEditor\Services\ThemeService;

class InstallCommand extends Command
{
    protected $signature = 'ai-editor:install 
                            {--stack= : The frontend stack to install (laravel-default, livewire, vue-js, react-nextjs)}
                            {--theme= : The theme to apply (default, dark, minimal, colorful, glassmorphism)}
                            {--no-deps : Skip installing dependencies}
                            {--no-migrate : Skip running migrations}
                            {--no-seed : Skip seeding database}
                            {--no-auth : Skip setting up authentication}
                            {--no-admin : Skip creating admin user}';

    protected $description = 'Install AI Text Editor with Multi-Stack Support - Laravel Default, Livewire, Vue.js, React+Next.js';

    public function handle(
        InstallerService $installerService,
        StackService $stackService,
        ThemeService $themeService
    ): int {
        $this->info('🚀 Laravel Multi-Stack Starter Kit Installer');
        $this->info('📦 Blade+Livewire • Vue.js SPA • React+Next.js');
        $this->newLine();

        // Get available stacks
        $availableStacks = $stackService->getAvailableStacks();
        
        // Select stack
        $stack = $this->selectStack($availableStacks);
        
        if (!$stack) {
            $this->error('No stack selected. Installation cancelled.');
            return 1;
        }

        // Get stack info
        $stackInfo = $stackService->getStackInfo($stack);
        $this->info("Selected: {$stackInfo['name']}");
        $this->line("Description: {$stackInfo['description']}");
        $this->newLine();

        // Select theme
        $theme = $this->selectTheme($themeService);

        // Installation options
        $options = $this->getInstallationOptions();

        // Confirm installation
        if (!$this->confirm('Do you want to proceed with the installation?')) {
            $this->info('Installation cancelled.');
            return 0;
        }

        // Show progress
        $this->info('Starting installation...');
        $this->newLine();

        // Install
        $result = $installerService->install($stack, $theme, $options);

        if ($result['success']) {
            $this->newLine();
            $this->info('✅ Installation completed successfully!');
            $this->newLine();
            
            // Show next steps
            foreach ($result['next_steps'] as $step) {
                $this->line($step);
            }
            
            return 0;
        } else {
            $this->newLine();
            $this->error('❌ Installation failed!');
            $this->error($result['error']);
            $this->newLine();
            
            // Show logs
            $this->info('Installation logs:');
            foreach ($result['logs'] as $log) {
                $this->line($log);
            }
            
            return 1;
        }
    }

    protected function selectStack(array $availableStacks): ?string
    {
        // Check if stack is provided via option
        $stack = $this->option('stack');
        
        if ($stack) {
            if (!in_array($stack, $availableStacks)) {
                $this->error("Invalid stack: {$stack}");
                $this->info('Available stacks: ' . implode(', ', $availableStacks));
                return null;
            }
            return $stack;
        }

        // Interactive selection
        $this->info('Choose your frontend stack:');
        $this->newLine();

        $choices = [];
        foreach ($availableStacks as $stack) {
            $stackInfo = $this->laravel->make(StackService::class)->getStackInfo($stack);
            $choices[] = "{$stack} - {$stackInfo['name']}";
        }

        $selected = $this->choice('Select a stack', $choices);
        
        // Extract stack key from choice
        $stackKey = explode(' - ', $selected)[0];
        
        return $stackKey;
    }

    protected function selectTheme(ThemeService $themeService): string
    {
        // Check if theme is provided via option
        $theme = $this->option('theme');
        
        if ($theme) {
            $availableThemes = $themeService->getAvailableThemes();
            if (!in_array($theme, $availableThemes)) {
                $this->error("Invalid theme: {$theme}");
                $this->info('Available themes: ' . implode(', ', $availableThemes));
                $theme = 'default';
            }
            return $theme;
        }

        // Interactive selection
        $this->info('Choose your theme:');
        $this->newLine();

        $availableThemes = $themeService->getAvailableThemes();
        $choices = [];

        foreach ($availableThemes as $themeKey) {
            $themeInfo = $themeService->getThemeInfo($themeKey);
            $choices[] = "{$themeKey} - {$themeInfo['name']}";
        }

        $selected = $this->choice('Select a theme', $choices);
        
        // Extract theme key from choice
        $themeKey = explode(' - ', $selected)[0];
        
        return $themeKey;
    }

    protected function getInstallationOptions(): array
    {
        $options = [
            'install_composer' => !$this->option('no-deps'),
            'install_npm' => !$this->option('no-deps'),
            'run_migrations' => !$this->option('no-migrate'),
            'seed_database' => !$this->option('no-seed'),
            'setup_authentication' => !$this->option('no-auth'),
            'create_admin_user' => !$this->option('no-admin'),
            'setup_theme' => true,
        ];

        // Show options summary
        $this->newLine();
        $this->info('Installation Options:');
        $this->line("Install Dependencies: " . ($options['install_composer'] ? '✅' : '❌'));
        $this->line("Run Migrations: " . ($options['run_migrations'] ? '✅' : '❌'));
        $this->line("Seed Database: " . ($options['seed_database'] ? '✅' : '❌'));
        $this->line("Setup Authentication: " . ($options['setup_authentication'] ? '✅' : '❌'));
        $this->line("Create Admin User: " . ($options['create_admin_user'] ? '✅' : '❌'));
        $this->line("Setup Theme: " . ($options['setup_theme'] ? '✅' : '❌'));

        return $options;
    }
}