<?php

use AiEditor\AiTextEditor\Services\InstallerService;
use AiEditor\AiTextEditor\Services\StackService;
use AiEditor\AiTextEditor\Services\ThemeService;
use AiEditor\AiTextEditor\Services\AiFeatureBuilderService;

beforeEach(function () {
    $this->installerService = app(InstallerService::class);
    $this->stackService = app(StackService::class);
    $this->themeService = app(ThemeService::class);
    $this->aiService = app(AiFeatureBuilderService::class);
});

describe('Installer Service', function () {
    it('can be instantiated', function () {
        expect($this->installerService)->toBeInstanceOf(InstallerService::class);
    });

    it('can get available stacks', function () {
        $stacks = $this->installerService->getAvailableStacks();
        
        expect($stacks)->toBeArray()
            ->toContain('blade-livewire')
            ->toContain('vue-spa')
            ->toContain('react-nextjs');
    });

    it('can get available themes', function () {
        $themes = $this->installerService->getAvailableThemes();
        
        expect($themes)->toBeArray()
            ->toContain('default')
            ->toContain('dark')
            ->toContain('minimal')
            ->toContain('colorful');
    });

    it('can get logs', function () {
        $logs = $this->installerService->getLogs();
        
        expect($logs)->toBeArray();
    });
});

describe('Stack Service', function () {
    it('can be instantiated', function () {
        expect($this->stackService)->toBeInstanceOf(StackService::class);
    });

    it('can get available stacks', function () {
        $stacks = $this->stackService->getAvailableStacks();
        
        expect($stacks)->toBeArray()
            ->toHaveCount(3)
            ->toContain('blade-livewire')
            ->toContain('vue-spa')
            ->toContain('react-nextjs');
    });

    it('can get stack info', function () {
        $info = $this->stackService->getStackInfo('blade-livewire');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('icon')
            ->toHaveKey('color')
            ->toHaveKey('dependencies')
            ->toHaveKey('features')
            ->toHaveKey('routes');
    });

    it('can validate stacks', function () {
        expect($this->stackService->isValidStack('blade-livewire'))->toBeTrue();
        expect($this->stackService->isValidStack('vue-spa'))->toBeTrue();
        expect($this->stackService->isValidStack('react-nextjs'))->toBeTrue();
        expect($this->stackService->isValidStack('invalid-stack'))->toBeFalse();
    });

    it('can get composer dependencies', function () {
        $deps = $this->stackService->getComposerDependencies('blade-livewire');
        
        expect($deps)->toBeArray()
            ->toContain('livewire/livewire')
            ->toContain('spatie/laravel-permission');
    });

    it('can get npm dependencies', function () {
        $deps = $this->stackService->getNpmDependencies('vue-spa');
        
        expect($deps)->toBeArray()
            ->toContain('vue@3')
            ->toContain('vue-router@4')
            ->toContain('pinia');
    });

    it('can get npm dependencies as associative array', function () {
        $deps = $this->stackService->getNpmDependencies('vue-spa', true);
        
        expect($deps)->toBeArray()
            ->toHaveKey('vue')
            ->toHaveKey('vue-router')
            ->toHaveKey('pinia');
    });

    it('can get package scripts', function () {
        $scripts = $this->stackService->getPackageScripts('react-nextjs');
        
        expect($scripts)->toBeArray()
            ->toHaveKey('dev')
            ->toHaveKey('build')
            ->toHaveKey('start')
            ->toHaveKey('lint')
            ->toHaveKey('type-check');
    });

    it('can get vite config', function () {
        $config = $this->stackService->getViteConfig('blade-livewire');
        
        expect($config)->toBeString()
            ->toContain('vite')
            ->toContain('laravel-vite-plugin');
    });

    it('can get tailwind config', function () {
        $config = $this->stackService->getTailwindConfig();
        
        expect($config)->toBeString()
            ->toContain('tailwindcss')
            ->toContain('content');
    });
});

describe('Theme Service', function () {
    it('can be instantiated', function () {
        expect($this->themeService)->toBeInstanceOf(ThemeService::class);
    });

    it('can get available themes', function () {
        $themes = $this->themeService->getAvailableThemes();
        
        expect($themes)->toBeArray()
            ->toHaveCount(4)
            ->toContain('default')
            ->toContain('dark')
            ->toContain('minimal')
            ->toContain('colorful');
    });

    it('can get theme info', function () {
        $info = $this->themeService->getThemeInfo('dark');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors');
    });

    it('can validate themes', function () {
        expect($this->themeService->isValidTheme('default'))->toBeTrue();
        expect($this->themeService->isValidTheme('dark'))->toBeTrue();
        expect($this->themeService->isValidTheme('minimal'))->toBeTrue();
        expect($this->themeService->isValidTheme('colorful'))->toBeTrue();
        expect($this->themeService->isValidTheme('invalid-theme'))->toBeFalse();
    });

    it('can apply theme', function () {
        expect(fn() => $this->themeService->applyTheme('default'))
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
    });

    it('throws exception for invalid theme preview', function () {
        expect(fn() => $this->themeService->getThemePreview('invalid-theme'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('can generate theme css', function () {
        $css = $this->themeService->generateThemeCss('dark');
        
        expect($css)->toBeString()
            ->toContain('Theme: Dark')
            ->toContain(':root')
            ->toContain('--color-primary');
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

describe('AI Feature Builder Service', function () {
    it('can be instantiated', function () {
        expect($this->aiService)->toBeInstanceOf(AiFeatureBuilderService::class);
    });

    it('can get available providers', function () {
        $providers = $this->aiService->getAvailableProviders();
        
        expect($providers)->toBeArray()
            ->toContain('openai')
            ->toContain('anthropic')
            ->toContain('google');
    });

    it('can get provider info', function () {
        $info = $this->aiService->getProviderInfo('openai');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('configured')
            ->toHaveKey('model');
    });

    it('throws exception for invalid provider', function () {
        expect(fn() => $this->aiService->getProviderInfo('invalid-provider'))
            ->toThrow(Exception::class);
    });
});
