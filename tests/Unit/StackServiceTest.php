<?php

use AiEditor\AiTextEditor\Services\StackService;

beforeEach(function () {
    $this->stackService = app(StackService::class);
});

describe('Stack Service', function () {
    it('can get available stacks', function () {
        $stacks = $this->stackService->getAvailableStacks();
        
        expect($stacks)->toBeArray()
            ->toHaveCount(3)
            ->toContain('blade-livewire')
            ->toContain('vue-spa')
            ->toContain('react-nextjs');
    });

    it('can get stack info for blade-livewire', function () {
        $info = $this->stackService->getStackInfo('blade-livewire');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('icon')
            ->toHaveKey('color')
            ->toHaveKey('dependencies')
            ->toHaveKey('features')
            ->toHaveKey('routes');
        
        expect($info['name'])->toBe('Blade + Livewire');
        expect($info['icon'])->toBe('⚡');
        expect($info['color'])->toBe('blue');
    });

    it('can get stack info for vue-spa', function () {
        $info = $this->stackService->getStackInfo('vue-spa');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('icon')
            ->toHaveKey('color');
        
        expect($info['name'])->toBe('Vue.js SPA');
        expect($info['icon'])->toBe('💚');
        expect($info['color'])->toBe('green');
    });

    it('can get stack info for react-nextjs', function () {
        $info = $this->stackService->getStackInfo('react-nextjs');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('description')
            ->toHaveKey('icon')
            ->toHaveKey('color');
        
        expect($info['name'])->toBe('React + Next.js');
        expect($info['icon'])->toBe('⚛️');
        expect($info['color'])->toBe('purple');
    });

    it('returns empty array for invalid stack', function () {
        $info = $this->stackService->getStackInfo('invalid-stack');
        
        expect($info)->toBeArray()
            ->toBeEmpty();
    });

    it('can validate stacks', function () {
        expect($this->stackService->isValidStack('blade-livewire'))->toBeTrue();
        expect($this->stackService->isValidStack('vue-spa'))->toBeTrue();
        expect($this->stackService->isValidStack('react-nextjs'))->toBeTrue();
        expect($this->stackService->isValidStack('invalid-stack'))->toBeFalse();
    });

    it('can get composer dependencies for blade-livewire', function () {
        $deps = $this->stackService->getComposerDependencies('blade-livewire');
        
        expect($deps)->toBeArray()
            ->toContain('livewire/livewire')
            ->toContain('spatie/laravel-permission')
            ->toContain('laravel/breeze');
    });

    it('can get composer dependencies for vue-spa', function () {
        $deps = $this->stackService->getComposerDependencies('vue-spa');
        
        expect($deps)->toBeArray()
            ->toContain('laravel/sanctum')
            ->toContain('spatie/laravel-permission')
            ->toContain('laravel/breeze');
    });

    it('can get composer dependencies for react-nextjs', function () {
        $deps = $this->stackService->getComposerDependencies('react-nextjs');
        
        expect($deps)->toBeArray()
            ->toContain('laravel/sanctum')
            ->toContain('spatie/laravel-permission')
            ->toContain('laravel/breeze');
    });

    it('returns empty array for invalid stack dependencies', function () {
        $deps = $this->stackService->getComposerDependencies('invalid-stack');
        
        expect($deps)->toBeArray()
            ->toBeEmpty();
    });

    it('can get npm dependencies for blade-livewire', function () {
        $deps = $this->stackService->getNpmDependencies('blade-livewire');
        
        expect($deps)->toBeArray()
            ->toContain('alpinejs')
            ->toContain('tailwindcss')
            ->toContain('@tailwindcss/forms')
            ->toContain('@tailwindcss/typography')
            ->toContain('axios');
    });

    it('can get npm dependencies for vue-spa', function () {
        $deps = $this->stackService->getNpmDependencies('vue-spa');
        
        expect($deps)->toBeArray()
            ->toContain('vue@3')
            ->toContain('vue-router@4')
            ->toContain('pinia')
            ->toContain('axios')
            ->toContain('tailwindcss')
            ->toContain('@tailwindcss/forms')
            ->toContain('vite')
            ->toContain('laravel-vite-plugin');
    });

    it('can get npm dependencies for react-nextjs', function () {
        $deps = $this->stackService->getNpmDependencies('react-nextjs');
        
        expect($deps)->toBeArray()
            ->toContain('next@14')
            ->toContain('react@18')
            ->toContain('react-dom@18')
            ->toContain('axios')
            ->toContain('tailwindcss')
            ->toContain('@tailwindcss/forms')
            ->toContain('typescript')
            ->toContain('@types/react')
            ->toContain('@types/node');
    });

    it('can get npm dependencies as associative array', function () {
        $deps = $this->stackService->getNpmDependencies('vue-spa', true);
        
        expect($deps)->toBeArray()
            ->toHaveKey('vue')
            ->toHaveKey('vue-router')
            ->toHaveKey('pinia')
            ->toHaveKey('axios');
        
        expect($deps['vue'])->toBe('3');
        expect($deps['vue-router'])->toBe('4');
    });

    it('can get package scripts for blade-livewire', function () {
        $scripts = $this->stackService->getPackageScripts('blade-livewire');
        
        expect($scripts)->toBeArray()
            ->toHaveKey('dev')
            ->toHaveKey('build')
            ->toHaveKey('preview');
        
        expect($scripts['dev'])->toBe('vite');
        expect($scripts['build'])->toBe('vite build');
    });

    it('can get package scripts for vue-spa', function () {
        $scripts = $this->stackService->getPackageScripts('vue-spa');
        
        expect($scripts)->toBeArray()
            ->toHaveKey('dev')
            ->toHaveKey('build')
            ->toHaveKey('preview')
            ->toHaveKey('serve')
            ->toHaveKey('type-check');
        
        expect($scripts['serve'])->toBe('vite --host');
        expect($scripts['type-check'])->toBe('vue-tsc --noEmit');
    });

    it('can get package scripts for react-nextjs', function () {
        $scripts = $this->stackService->getPackageScripts('react-nextjs');
        
        expect($scripts)->toBeArray()
            ->toHaveKey('dev')
            ->toHaveKey('build')
            ->toHaveKey('preview')
            ->toHaveKey('start')
            ->toHaveKey('lint')
            ->toHaveKey('type-check');
        
        expect($scripts['start'])->toBe('next start');
        expect($scripts['lint'])->toBe('next lint');
        expect($scripts['type-check'])->toBe('tsc --noEmit');
    });

    it('can get vite config for blade-livewire', function () {
        $config = $this->stackService->getViteConfig('blade-livewire');
        
        expect($config)->toBeString()
            ->toContain('vite')
            ->toContain('laravel-vite-plugin')
            ->toContain('resources/css/app.css')
            ->toContain('resources/js/app.js');
    });

    it('can get vite config for vue-spa', function () {
        $config = $this->stackService->getViteConfig('vue-spa');
        
        expect($config)->toBeString()
            ->toContain('vite')
            ->toContain('laravel-vite-plugin')
            ->toContain('@vitejs/plugin-vue')
            ->toContain('vue');
    });

    it('can get vite config for react-nextjs', function () {
        $config = $this->stackService->getViteConfig('react-nextjs');
        
        expect($config)->toBeString()
            ->toContain('next')
            ->toContain('react')
            ->toContain('typescript');
    });

    it('can get tailwind config', function () {
        $config = $this->stackService->getTailwindConfig();
        
        expect($config)->toBeString()
            ->toContain('tailwindcss')
            ->toContain('content')
            ->toContain('resources/**/*.blade.php')
            ->toContain('resources/**/*.js')
            ->toContain('resources/**/*.vue')
            ->toContain('resources/**/*.tsx')
            ->toContain('@tailwindcss/forms')
            ->toContain('@tailwindcss/typography');
    });
});
