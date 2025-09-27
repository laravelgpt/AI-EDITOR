<?php

namespace LaravelStarterKit\MultiStack;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use LaravelStarterKit\MultiStack\Services\InstallerService;
use LaravelStarterKit\MultiStack\Services\StackService;
use LaravelStarterKit\MultiStack\Services\ThemeService;
use LaravelStarterKit\MultiStack\Console\Commands\InstallCommand;
use LaravelStarterKit\MultiStack\Console\Commands\WebInstallerCommand;
use LaravelStarterKit\MultiStack\Console\Commands\StackCommand;
use LaravelStarterKit\MultiStack\Console\Commands\ThemeCommand;
use LaravelStarterKit\MultiStack\Http\Controllers\InstallerController;
use LaravelStarterKit\MultiStack\Http\Controllers\StackController;
use LaravelStarterKit\MultiStack\Http\Controllers\ThemeController;

class MultiStackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/multi-stack.php', 'multi-stack');
        
        $this->app->singleton(InstallerService::class);
        $this->app->singleton(StackService::class);
        $this->app->singleton(ThemeService::class);
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/multi-stack.php' => config_path('multi-stack.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/multi-stack'),
        ], 'assets');

        // Publish stubs
        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/multi-stack'),
        ], 'stubs');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                WebInstallerCommand::class,
                StackCommand::class,
                ThemeCommand::class,
            ]);
        }

        // Load routes
        $this->loadRoutes();

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'multi-stack');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function loadRoutes(): void
    {
        // Installer routes
        Route::prefix('multi-stack')
            ->middleware(['web'])
            ->group(function () {
                Route::get('/installer', [InstallerController::class, 'index'])->name('multi-stack.installer');
                Route::post('/installer/select-stack', [InstallerController::class, 'selectStack'])->name('multi-stack.select-stack');
                Route::post('/installer/install', [InstallerController::class, 'install'])->name('multi-stack.install');
                Route::get('/installer/progress/{id}', [InstallerController::class, 'progress'])->name('multi-stack.progress');
                Route::get('/installer/complete', [InstallerController::class, 'complete'])->name('multi-stack.complete');
            });

        // Stack management routes
        Route::prefix('multi-stack/stacks')
            ->middleware(['web', 'auth'])
            ->group(function () {
                Route::get('/', [StackController::class, 'index'])->name('multi-stack.stacks.index');
                Route::post('/switch', [StackController::class, 'switch'])->name('multi-stack.stacks.switch');
                Route::get('/status', [StackController::class, 'status'])->name('multi-stack.stacks.status');
            });

        // Theme management routes
        Route::prefix('multi-stack/themes')
            ->middleware(['web', 'auth'])
            ->group(function () {
                Route::get('/', [ThemeController::class, 'index'])->name('multi-stack.themes.index');
                Route::post('/apply', [ThemeController::class, 'apply'])->name('multi-stack.themes.apply');
                Route::get('/preview/{theme}', [ThemeController::class, 'preview'])->name('multi-stack.themes.preview');
            });
    }
}