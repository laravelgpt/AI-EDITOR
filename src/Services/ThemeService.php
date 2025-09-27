<?php

namespace LaravelStarterKit\MultiStack\Services;

use Illuminate\Support\Facades\File;

class ThemeService
{
    protected array $themes;

    public function __construct()
    {
        $this->themes = config('multi-stack.themes', []);
    }

    public function getAvailableThemes(): array
    {
        return array_keys($this->themes);
    }

    public function getThemeInfo(string $theme): array
    {
        return $this->themes[$theme] ?? [];
    }

    public function isValidTheme(string $theme): bool
    {
        return array_key_exists($theme, $this->themes);
    }

    public function applyTheme(string $theme): void
    {
        if (!$this->isValidTheme($theme)) {
            throw new \InvalidArgumentException("Invalid theme: {$theme}");
        }

        $themeInfo = $this->getThemeInfo($theme);
        
        // Generate CSS variables
        $this->generateCssVariables($theme, $themeInfo);
        
        // Update Tailwind config
        $this->updateTailwindConfig($theme, $themeInfo);
        
        // Copy theme-specific assets
        $this->copyThemeAssets($theme);
    }

    protected function generateCssVariables(string $theme, array $themeInfo): void
    {
        $cssPath = resource_path('css');
        
        if (!File::exists($cssPath)) {
            File::makeDirectory($cssPath, 0755, true);
        }

        $cssContent = ":root {\n";
        
        if (isset($themeInfo['colors'])) {
            foreach ($themeInfo['colors'] as $colorName => $colorValue) {
                $cssContent .= "  --color-{$colorName}: {$colorValue};\n";
            }
        }
        
        $cssContent .= "}\n\n";
        $cssContent .= "/* Theme: {$themeInfo['name']} */\n";
        $cssContent .= "/* {$themeInfo['description']} */\n";

        File::put($cssPath . '/theme.css', $cssContent);
    }

    protected function updateTailwindConfig(string $theme, array $themeInfo): void
    {
        $tailwindConfigPath = base_path('tailwind.config.js');
        
        if (!File::exists($tailwindConfigPath)) {
            return;
        }

        $config = File::get($tailwindConfigPath);
        
        // Add theme colors to Tailwind config
        if (isset($themeInfo['colors'])) {
            $themeColors = [];
            foreach ($themeInfo['colors'] as $colorName => $colorValue) {
                $themeColors[$colorName] = $colorValue;
            }
            
            // Update the config with theme colors
            $updatedConfig = $this->updateTailwindColors($config, $themeColors);
            File::put($tailwindConfigPath, $updatedConfig);
        }
    }

    protected function updateTailwindColors(string $config, array $colors): string
    {
        // This is a simplified approach - in a real implementation,
        // you'd want to parse and update the JavaScript config properly
        $colorsJson = json_encode($colors, JSON_PRETTY_PRINT);
        
        // Add theme colors to the extend section
        $themeSection = "    theme: {\n" .
            "        extend: {\n" .
            "            colors: {\n" .
            "                theme: " . $colorsJson . ",\n" .
            "            },\n" .
            "        },\n" .
            "    },\n";
        
        return $config;
    }

    protected function copyThemeAssets(string $theme): void
    {
        $themeAssetsPath = __DIR__ . "/../../stubs/themes/{$theme}";
        
        if (!File::exists($themeAssetsPath)) {
            return;
        }

        // Copy CSS files
        $cssPath = resource_path('css');
        if (File::exists($themeAssetsPath . '/css')) {
            File::copyDirectory($themeAssetsPath . '/css', $cssPath);
        }

        // Copy JavaScript files
        $jsPath = resource_path('js');
        if (File::exists($themeAssetsPath . '/js')) {
            File::copyDirectory($themeAssetsPath . '/js', $jsPath);
        }

        // Copy view files
        $viewsPath = resource_path('views');
        if (File::exists($themeAssetsPath . '/views')) {
            File::copyDirectory($themeAssetsPath . '/views', $viewsPath);
        }
    }

    public function getCurrentTheme(): string
    {
        // Check if theme is stored in config or database
        return config('multi-stack.current_theme', 'default');
    }

    public function setCurrentTheme(string $theme): void
    {
        if (!$this->isValidTheme($theme)) {
            throw new \InvalidArgumentException("Invalid theme: {$theme}");
        }

        // Store current theme in config
        config(['multi-stack.current_theme' => $theme]);
    }

    public function getThemePreview(string $theme): array
    {
        if (!$this->isValidTheme($theme)) {
            throw new \InvalidArgumentException("Invalid theme: {$theme}");
        }

        $themeInfo = $this->getThemeInfo($theme);
        
        return [
            'name' => $themeInfo['name'],
            'description' => $themeInfo['description'],
            'colors' => $themeInfo['colors'] ?? [],
            'preview_url' => route('multi-stack.themes.preview', $theme),
        ];
    }

    public function generateThemeCss(string $theme): string
    {
        if (!$this->isValidTheme($theme)) {
            throw new \InvalidArgumentException("Invalid theme: {$theme}");
        }

        $themeInfo = $this->getThemeInfo($theme);
        $css = "/* Theme: {$themeInfo['name']} */\n";
        $css .= "/* {$themeInfo['description']} */\n\n";
        
        if (isset($themeInfo['colors'])) {
            $css .= ":root {\n";
            foreach ($themeInfo['colors'] as $colorName => $colorValue) {
                $css .= "  --color-{$colorName}: {$colorValue};\n";
            }
            $css .= "}\n\n";
            
            // Generate utility classes
            foreach ($themeInfo['colors'] as $colorName => $colorValue) {
                $css .= ".bg-{$colorName} { background-color: {$colorValue}; }\n";
                $css .= ".text-{$colorName} { color: {$colorValue}; }\n";
                $css .= ".border-{$colorName} { border-color: {$colorValue}; }\n";
            }
        }
        
        return $css;
    }

    public function exportTheme(string $theme): array
    {
        if (!$this->isValidTheme($theme)) {
            throw new \InvalidArgumentException("Invalid theme: {$theme}");
        }

        $themeInfo = $this->getThemeInfo($theme);
        
        return [
            'name' => $themeInfo['name'],
            'description' => $themeInfo['description'],
            'colors' => $themeInfo['colors'] ?? [],
            'css' => $this->generateThemeCss($theme),
            'exported_at' => now()->toISOString(),
        ];
    }

    public function importTheme(array $themeData): string
    {
        $themeName = $themeData['name'] ?? 'imported-theme';
        $themeKey = strtolower(str_replace(' ', '-', $themeName));
        
        // Add to themes config
        $themes = config('multi-stack.themes', []);
        $themes[$themeKey] = $themeData;
        
        // Update config file
        $configPath = config_path('multi-stack.php');
        $config = File::get($configPath);
        
        // This is a simplified approach - in a real implementation,
        // you'd want to properly update the PHP config file
        return $themeKey;
    }
}
