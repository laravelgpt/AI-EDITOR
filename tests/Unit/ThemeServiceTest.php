<?php

use AiEditor\AiTextEditor\Services\ThemeService;

beforeEach(function () {
    $this->themeService = app(ThemeService::class);
});

describe('Theme Service', function () {
    it('can get available themes', function () {
        $themes = $this->themeService->getAvailableThemes();
        
        expect($themes)->toBeArray()
            ->toHaveCount(4)
            ->toContain('default')
            ->toContain('dark')
            ->toContain('minimal')
            ->toContain('colorful');
    });

    it('can get theme info for default theme', function () {
        $info = $this->themeService->getThemeInfo('default');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors');
        
        expect($info['name'])->toBe('Default');
        expect($info['description'])->toBe('Clean and modern default theme');
        expect($info['colors'])->toBeArray()
            ->toHaveKey('primary')
            ->toHaveKey('secondary')
            ->toHaveKey('accent')
            ->toHaveKey('background')
            ->toHaveKey('surface')
            ->toHaveKey('text');
    });

    it('can get theme info for dark theme', function () {
        $info = $this->themeService->getThemeInfo('dark');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors');
        
        expect($info['name'])->toBe('Dark');
        expect($info['description'])->toBe('Dark theme with modern aesthetics');
        expect($info['colors']['primary'])->toBe('#60a5fa');
        expect($info['colors']['background'])->toBe('#111827');
    });

    it('can get theme info for minimal theme', function () {
        $info = $this->themeService->getThemeInfo('minimal');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors');
        
        expect($info['name'])->toBe('Minimal');
        expect($info['description'])->toBe('Minimalist design with clean lines');
        expect($info['colors']['primary'])->toBe('#000000');
    });

    it('can get theme info for colorful theme', function () {
        $info = $this->themeService->getThemeInfo('colorful');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors');
        
        expect($info['name'])->toBe('Colorful');
        expect($info['description'])->toBe('Vibrant theme with multiple colors');
        expect($info['colors']['primary'])->toBe('#8b5cf6');
        expect($info['colors']['background'])->toBe('#fef3c7');
    });

    it('returns empty array for invalid theme', function () {
        $info = $this->themeService->getThemeInfo('invalid-theme');
        
        expect($info)->toBeArray()
            ->toBeEmpty();
    });

    it('can validate themes', function () {
        expect($this->themeService->isValidTheme('default'))->toBeTrue();
        expect($this->themeService->isValidTheme('dark'))->toBeTrue();
        expect($this->themeService->isValidTheme('minimal'))->toBeTrue();
        expect($this->themeService->isValidTheme('colorful'))->toBeTrue();
        expect($this->themeService->isValidTheme('invalid-theme'))->toBeFalse();
    });

    it('can apply theme without throwing exception', function () {
        expect(fn() => $this->themeService->applyTheme('default'))
            ->not->toThrow(Exception::class);
        
        expect(fn() => $this->themeService->applyTheme('dark'))
            ->not->toThrow(Exception::class);
        
        expect(fn() => $this->themeService->applyTheme('minimal'))
            ->not->toThrow(Exception::class);
        
        expect(fn() => $this->themeService->applyTheme('colorful'))
            ->not->toThrow(Exception::class);
    });

    it('throws exception for invalid theme', function () {
        expect(fn() => $this->themeService->applyTheme('invalid-theme'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('can get current theme', function () {
        $currentTheme = $this->themeService->getCurrentTheme();
        
        expect($currentTheme)->toBeString();
    });

    it('can set current theme', function () {
        $this->themeService->setCurrentTheme('dark');
        
        expect($this->themeService->getCurrentTheme())->toBe('dark');
        
        $this->themeService->setCurrentTheme('minimal');
        
        expect($this->themeService->getCurrentTheme())->toBe('minimal');
    });

    it('throws exception when setting invalid theme', function () {
        expect(fn() => $this->themeService->setCurrentTheme('invalid-theme'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('can get theme preview', function () {
        $preview = $this->themeService->getThemePreview('dark');
        
        expect($preview)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors')
            ->toHaveKey('preview_url');
        
        expect($preview['name'])->toBe('Dark');
        expect($preview['colors'])->toBeArray();
    });

    it('throws exception for invalid theme preview', function () {
        expect(fn() => $this->themeService->getThemePreview('invalid-theme'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('can generate theme css', function () {
        $css = $this->themeService->generateThemeCss('dark');
        
        expect($css)->toBeString()
            ->toContain('Theme: Dark')
            ->toContain('Dark theme with modern aesthetics')
            ->toContain(':root')
            ->toContain('--color-primary')
            ->toContain('--color-secondary')
            ->toContain('--color-accent')
            ->toContain('--color-background')
            ->toContain('--color-surface')
            ->toContain('--color-text');
    });

    it('throws exception for invalid theme css generation', function () {
        expect(fn() => $this->themeService->generateThemeCss('invalid-theme'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('can export theme', function () {
        $export = $this->themeService->exportTheme('default');
        
        expect($export)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors')
            ->toHaveKey('css')
            ->toHaveKey('exported_at');
        
        expect($export['name'])->toBe('Default');
        expect($export['css'])->toBeString();
        expect($export['exported_at'])->toBeString();
    });

    it('throws exception for invalid theme export', function () {
        expect(fn() => $this->themeService->exportTheme('invalid-theme'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('can import theme', function () {
        $themeData = [
            'name' => 'Custom Theme',
            'description' => 'A custom theme',
            'colors' => [
                'primary' => '#ff0000',
                'secondary' => '#00ff00',
                'accent' => '#0000ff',
                'background' => '#ffffff',
                'surface' => '#f0f0f0',
                'text' => '#000000'
            ]
        ];
        
        $themeKey = $this->themeService->importTheme($themeData);
        
        expect($themeKey)->toBeString()
            ->toBe('custom-theme');
    });
});
