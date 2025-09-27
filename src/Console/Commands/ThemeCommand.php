<?php

namespace LaravelStarterKit\MultiStack\Console\Commands;

use Illuminate\Console\Command;
use LaravelStarterKit\MultiStack\Services\ThemeService;

class ThemeCommand extends Command
{
    protected $signature = 'multi-stack:themes 
                            {theme? : Show details for a specific theme}
                            {--apply= : Apply a specific theme}
                            {--list : List all available themes}';

    protected $description = 'Manage themes for Laravel Multi-Stack Starter Kit';

    public function handle(ThemeService $themeService): int
    {
        $theme = $this->argument('theme');
        $apply = $this->option('apply');
        $list = $this->option('list');

        if ($apply) {
            return $this->applyTheme($apply, $themeService);
        }

        if ($list) {
            return $this->listThemes($themeService);
        }

        if ($theme) {
            return $this->showThemeDetails($theme, $themeService);
        }

        return $this->listThemes($themeService);
    }

    protected function listThemes(ThemeService $themeService): int
    {
        $this->info('🎨 Available Themes');
        $this->newLine();

        $themes = $themeService->getAvailableThemes();

        foreach ($themes as $themeKey) {
            $themeInfo = $themeService->getThemeInfo($themeKey);
            
            $this->line("<comment>{$themeKey}</comment>");
            $this->line("  Name: {$themeInfo['name']}");
            $this->line("  Description: {$themeInfo['description']}");
            
            if (isset($themeInfo['colors'])) {
                $this->line("  Colors:");
                foreach ($themeInfo['colors'] as $colorName => $colorValue) {
                    $this->line("    {$colorName}: {$colorValue}");
                }
            }
            
            $this->newLine();
        }

        $this->info('Use "php artisan multi-stack:themes <theme>" to see detailed information about a specific theme.');
        $this->info('Use "php artisan multi-stack:themes --apply=<theme>" to apply a theme.');
        
        return 0;
    }

    protected function showThemeDetails(string $theme, ThemeService $themeService): int
    {
        if (!$themeService->isValidTheme($theme)) {
            $this->error("Invalid theme: {$theme}");
            $this->info('Available themes: ' . implode(', ', $themeService->getAvailableThemes()));
            return 1;
        }

        $themeInfo = $themeService->getThemeInfo($theme);

        $this->info("🎨 Theme Details: {$themeInfo['name']}");
        $this->newLine();

        $this->line("Description: {$themeInfo['description']}");
        $this->newLine();

        if (isset($themeInfo['colors'])) {
            $this->info('Color Palette:');
            foreach ($themeInfo['colors'] as $colorName => $colorValue) {
                $this->line("  {$colorName}: {$colorValue}");
            }
            $this->newLine();
        }

        $this->info("To apply this theme, run: php artisan multi-stack:themes --apply={$theme}");

        return 0;
    }

    protected function applyTheme(string $theme, ThemeService $themeService): int
    {
        if (!$themeService->isValidTheme($theme)) {
            $this->error("Invalid theme: {$theme}");
            $this->info('Available themes: ' . implode(', ', $themeService->getAvailableThemes()));
            return 1;
        }

        $this->info("Applying theme: {$theme}");

        try {
            $themeService->applyTheme($theme);
            $this->info("✅ Theme '{$theme}' applied successfully!");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to apply theme: {$e->getMessage()}");
            return 1;
        }
    }
}
