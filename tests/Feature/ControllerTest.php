<?php

use LaravelStarterKit\MultiStack\Http\Controllers\InstallerController;
use LaravelStarterKit\MultiStack\Http\Controllers\StackController;
use LaravelStarterKit\MultiStack\Http\Controllers\ThemeController;

beforeEach(function () {
    $this->installerController = app(InstallerController::class);
    $this->stackController = app(StackController::class);
    $this->themeController = app(ThemeController::class);
});

describe('Installer Controller', function () {
    it('can be instantiated', function () {
        expect($this->installerController)->toBeInstanceOf(InstallerController::class);
    });

    it('can get index view', function () {
        $response = $this->get('/multi-stack/installer');
        
        expect($response->status())->toBe(200);
        expect($response->view())->toBe('multi-stack::installer.index');
    });

    it('can select stack', function () {
        $response = $this->post('/multi-stack/installer/select-stack', [
            'stack' => 'blade-livewire'
        ]);
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('stack')
            ->toHaveKey('info')
            ->toHaveKey('dependencies')
            ->toHaveKey('scripts');
        
        expect($response->json('success'))->toBeTrue();
        expect($response->json('stack'))->toBe('blade-livewire');
    });

    it('validates stack selection', function () {
        $response = $this->post('/multi-stack/installer/select-stack', [
            'stack' => 'invalid-stack'
        ]);
        
        expect($response->status())->toBe(422);
    });

    it('can start installation', function () {
        $response = $this->post('/multi-stack/installer/install', [
            'stack' => 'blade-livewire',
            'theme' => 'default',
            'options' => [
                'install_composer' => false,
                'install_npm' => false,
                'run_migrations' => false,
                'seed_database' => false,
                'setup_authentication' => false,
                'create_admin_user' => false,
            ]
        ]);
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('installation_id')
            ->toHaveKey('message');
        
        expect($response->json('success'))->toBeTrue();
    });

    it('validates installation request', function () {
        $response = $this->post('/multi-stack/installer/install', [
            'stack' => 'invalid-stack',
            'theme' => 'invalid-theme'
        ]);
        
        expect($response->status())->toBe(422);
    });

    it('can get installation progress', function () {
        // First start an installation
        $installResponse = $this->post('/multi-stack/installer/install', [
            'stack' => 'blade-livewire',
            'theme' => 'default',
            'options' => [
                'install_composer' => false,
                'install_npm' => false,
                'run_migrations' => false,
                'seed_database' => false,
                'setup_authentication' => false,
                'create_admin_user' => false,
            ]
        ]);
        
        $installationId = $installResponse->json('installation_id');
        
        // Then check progress
        $response = $this->get("/multi-stack/installer/progress/{$installationId}");
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('installation');
    });

    it('returns 404 for non-existent installation', function () {
        $response = $this->get('/multi-stack/installer/progress/invalid-id');
        
        expect($response->status())->toBe(404);
    });

    it('can get complete view', function () {
        $response = $this->get('/multi-stack/installer/complete');
        
        expect($response->status())->toBe(200);
        expect($response->view())->toBe('multi-stack::installer.complete');
    });
});

describe('Stack Controller', function () {
    it('can be instantiated', function () {
        expect($this->stackController)->toBeInstanceOf(StackController::class);
    });

    it('can get stacks index', function () {
        $response = $this->get('/multi-stack/stacks');
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('stacks');
        
        expect($response->json('success'))->toBeTrue();
        expect($response->json('stacks'))->toBeArray();
    });

    it('can switch stack', function () {
        $response = $this->post('/multi-stack/stacks/switch', [
            'stack' => 'vue-spa'
        ]);
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('message')
            ->toHaveKey('stack');
        
        expect($response->json('success'))->toBeTrue();
        expect($response->json('stack'))->toBe('vue-spa');
    });

    it('validates stack switch', function () {
        $response = $this->post('/multi-stack/stacks/switch', [
            'stack' => 'invalid-stack'
        ]);
        
        expect($response->status())->toBe(422);
    });

    it('can get stack status', function () {
        $response = $this->get('/multi-stack/stacks/status');
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('current_stack')
            ->toHaveKey('stack_info')
            ->toHaveKey('dependencies');
        
        expect($response->json('success'))->toBeTrue();
    });
});

describe('Theme Controller', function () {
    it('can be instantiated', function () {
        expect($this->themeController)->toBeInstanceOf(ThemeController::class);
    });

    it('can get themes index', function () {
        $response = $this->get('/multi-stack/themes');
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('themes')
            ->toHaveKey('current_theme');
        
        expect($response->json('success'))->toBeTrue();
        expect($response->json('themes'))->toBeArray();
    });

    it('can apply theme', function () {
        $response = $this->post('/multi-stack/themes/apply', [
            'theme' => 'dark'
        ]);
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('message')
            ->toHaveKey('theme')
            ->toHaveKey('theme_info');
        
        expect($response->json('success'))->toBeTrue();
        expect($response->json('theme'))->toBe('dark');
    });

    it('validates theme application', function () {
        $response = $this->post('/multi-stack/themes/apply', [
            'theme' => 'invalid-theme'
        ]);
        
        expect($response->status())->toBe(422);
    });

    it('can preview theme', function () {
        $response = $this->get('/multi-stack/themes/preview/dark');
        
        expect($response->status())->toBe(200);
        expect($response->json())->toHaveKey('success')
            ->toHaveKey('preview');
        
        expect($response->json('success'))->toBeTrue();
    });

    it('returns 500 for invalid theme preview', function () {
        $response = $this->get('/multi-stack/themes/preview/invalid-theme');
        
        expect($response->status())->toBe(500);
    });
});
