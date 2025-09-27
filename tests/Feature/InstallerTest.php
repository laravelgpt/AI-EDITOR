<?php

use AiEditor\AiTextEditor\Services\InstallerService;
use AiEditor\AiTextEditor\Services\StackService;
use AiEditor\AiTextEditor\Services\ThemeService;

beforeEach(function () {
    $this->installerService = app(InstallerService::class);
    $this->stackService = app(StackService::class);
    $this->themeService = app(ThemeService::class);
});

describe('Installer Service', function () {
    it('can get available stacks', function () {
        $stacks = $this->stackService->getAvailableStacks();
        
        expect($stacks)->toBeArray()
            ->toContain('blade-livewire')
            ->toContain('vue-spa')
            ->toContain('react-nextjs');
    });

    it('can get stack info', function () {
        $stackInfo = $this->stackService->getStackInfo('blade-livewire');
        
        expect($stackInfo)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('dependencies')
            ->toHaveKey('features');
    });

    it('can validate stack', function () {
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

    it('can get package scripts', function () {
        $scripts = $this->stackService->getPackageScripts('react-nextjs');
        
        expect($scripts)->toBeArray()
            ->toHaveKey('dev')
            ->toHaveKey('build')
            ->toHaveKey('start');
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
    it('can get available themes', function () {
        $themes = $this->themeService->getAvailableThemes();
        
        expect($themes)->toBeArray()
            ->toContain('default')
            ->toContain('dark')
            ->toContain('minimal')
            ->toContain('colorful');
    });

    it('can get theme info', function () {
        $themeInfo = $this->themeService->getThemeInfo('dark');
        
        expect($themeInfo)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('colors');
    });

    it('can validate theme', function () {
        expect($this->themeService->isValidTheme('default'))->toBeTrue();
        expect($this->themeService->isValidTheme('dark'))->toBeTrue();
        expect($this->themeService->isValidTheme('invalid-theme'))->toBeFalse();
    });

    it('can apply theme', function () {
        expect(fn() => $this->themeService->applyTheme('default'))
            ->not->toThrow(Exception::class);
    });

    it('can get current theme', function () {
        $currentTheme = $this->themeService->getCurrentTheme();
        
        expect($currentTheme)->toBeString();
    });

    it('can set current theme', function () {
        $this->themeService->setCurrentTheme('dark');
        
        expect($this->themeService->getCurrentTheme())->toBe('dark');
    });
});

describe('Installer Integration', function () {
    it('can install blade-livewire stack', function () {
        $result = $this->installerService->install('blade-livewire', 'default', [
            'install_composer' => false,
            'install_npm' => false,
            'run_migrations' => false,
            'seed_database' => false,
            'setup_authentication' => false,
            'create_admin_user' => false,
        ]);

        expect($result)->toHaveKey('success')
            ->toHaveKey('stack')
            ->toHaveKey('theme')
            ->toHaveKey('logs')
            ->toHaveKey('next_steps');

        expect($result['success'])->toBeTrue();
        expect($result['stack'])->toBe('blade-livewire');
        expect($result['theme'])->toBe('default');
    });

    it('can install vue-spa stack', function () {
        $result = $this->installerService->install('vue-spa', 'dark', [
            'install_composer' => false,
            'install_npm' => false,
            'run_migrations' => false,
            'seed_database' => false,
            'setup_authentication' => false,
            'create_admin_user' => false,
        ]);

        expect($result)->toHaveKey('success')
            ->toHaveKey('stack')
            ->toHaveKey('theme');

        expect($result['success'])->toBeTrue();
        expect($result['stack'])->toBe('vue-spa');
        expect($result['theme'])->toBe('dark');
    });

    it('can install react-nextjs stack', function () {
        $result = $this->installerService->install('react-nextjs', 'minimal', [
            'install_composer' => false,
            'install_npm' => false,
            'run_migrations' => false,
            'seed_database' => false,
            'setup_authentication' => false,
            'create_admin_user' => false,
        ]);

        expect($result)->toHaveKey('success')
            ->toHaveKey('stack')
            ->toHaveKey('theme');

        expect($result['success'])->toBeTrue();
        expect($result['stack'])->toBe('react-nextjs');
        expect($result['theme'])->toBe('minimal');
    });

    it('handles invalid stack gracefully', function () {
        $result = $this->installerService->install('invalid-stack', 'default', []);

        expect($result)->toHaveKey('success');
        expect($result['success'])->toBeFalse();
        expect($result)->toHaveKey('error');
    });

    it('handles invalid theme gracefully', function () {
        $result = $this->installerService->install('blade-livewire', 'invalid-theme', []);

        expect($result)->toHaveKey('success');
        expect($result['success'])->toBeFalse();
        expect($result)->toHaveKey('error');
    });
});
