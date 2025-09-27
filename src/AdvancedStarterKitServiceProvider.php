<?php

namespace LaravelDynamicStarterKit\Advanced;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use LaravelDynamicStarterKit\Advanced\Services\FeatureService;
use LaravelDynamicStarterKit\Advanced\Services\AiService;
use LaravelDynamicStarterKit\Advanced\Services\StackService;
use LaravelDynamicStarterKit\Advanced\Services\InstallerService;
use LaravelDynamicStarterKit\Advanced\Console\Commands\InstallCommand;
use LaravelDynamicStarterKit\Advanced\Console\Commands\FeatureCommand;
use LaravelDynamicStarterKit\Advanced\Console\Commands\AiCommand;
use LaravelDynamicStarterKit\Advanced\Http\Controllers\AdminController;
use LaravelDynamicStarterKit\Advanced\Http\Controllers\FeatureController;
use LaravelDynamicStarterKit\Advanced\Http\Controllers\AiController;

class AdvancedStarterKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/advanced-starter-kit.php', 'advanced-starter-kit');
        
        $this->app->singleton(FeatureService::class);
        $this->app->singleton(AiService::class);
        $this->app->singleton(StackService::class);
        $this->app->singleton(InstallerService::class);
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/advanced-starter-kit.php' => config_path('advanced-starter-kit.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/advanced-starter-kit'),
        ], 'assets');

        // Publish stubs
        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/advanced-starter-kit'),
        ], 'stubs');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                FeatureCommand::class,
                AiCommand::class,
            ]);
        }

        // Load routes
        $this->loadRoutes();

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'advanced-starter-kit');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register dynamic features
        $this->registerDynamicFeatures();
    }

    protected function loadRoutes(): void
    {
        // Admin routes
        Route::prefix('admin')
            ->middleware(['web', 'auth', 'role:admin'])
            ->group(function () {
                Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
                Route::get('/features', [AdminController::class, 'features'])->name('admin.features');
                Route::get('/ai-builder', [AdminController::class, 'aiBuilder'])->name('admin.ai-builder');
                Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
            });

        // Feature management routes
        Route::prefix('admin/features')
            ->middleware(['web', 'auth', 'role:admin'])
            ->group(function () {
                Route::get('/', [FeatureController::class, 'index'])->name('admin.features.index');
                Route::post('/', [FeatureController::class, 'store'])->name('admin.features.store');
                Route::get('/{feature}', [FeatureController::class, 'show'])->name('admin.features.show');
                Route::put('/{feature}', [FeatureController::class, 'update'])->name('admin.features.update');
                Route::delete('/{feature}', [FeatureController::class, 'destroy'])->name('admin.features.destroy');
                Route::post('/{feature}/toggle', [FeatureController::class, 'toggle'])->name('admin.features.toggle');
                Route::post('/{feature}/regenerate', [FeatureController::class, 'regenerate'])->name('admin.features.regenerate');
            });

        // AI routes
        Route::prefix('admin/ai')
            ->middleware(['web', 'auth', 'role:admin'])
            ->group(function () {
                Route::post('/generate-feature', [AiController::class, 'generateFeature'])->name('admin.ai.generate-feature');
                Route::post('/update-feature', [AiController::class, 'updateFeature'])->name('admin.ai.update-feature');
                Route::post('/generate-content', [AiController::class, 'generateContent'])->name('admin.ai.generate-content');
            });

        // API routes for SPA
        Route::prefix('api/v1')
            ->middleware(['api', 'auth:sanctum'])
            ->group(function () {
                Route::get('/features', [FeatureController::class, 'apiIndex']);
                Route::get('/features/{feature}', [FeatureController::class, 'apiShow']);
                Route::post('/ai/generate', [AiController::class, 'apiGenerate']);
            });
    }

    protected function registerDynamicFeatures(): void
    {
        // This will be populated by the FeatureService
        // Features are registered dynamically based on what's enabled
        $this->app->make(FeatureService::class)->registerDynamicRoutes();
    }
}
