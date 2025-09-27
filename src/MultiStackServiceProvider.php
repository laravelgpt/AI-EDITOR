<?php

namespace AiEditor\AiTextEditor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use AiEditor\AiTextEditor\Services\InstallerService;
use AiEditor\AiTextEditor\Services\StackService;
use AiEditor\AiTextEditor\Services\ThemeService;
use AiEditor\AiTextEditor\Console\Commands\InstallCommand;
use AiEditor\AiTextEditor\Console\Commands\WebInstallerCommand;
use AiEditor\AiTextEditor\Console\Commands\StackCommand;
use AiEditor\AiTextEditor\Console\Commands\ThemeCommand;
use AiEditor\AiTextEditor\Http\Controllers\InstallerController;
use AiEditor\AiTextEditor\Http\Controllers\StackController;
use AiEditor\AiTextEditor\Http\Controllers\ThemeController;

class AiTextEditorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-text-editor.php', 'ai-text-editor');
        
        $this->app->singleton(InstallerService::class);
        $this->app->singleton(StackService::class);
        $this->app->singleton(ThemeService::class);
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/ai-text-editor.php' => config_path('ai-text-editor.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/ai-text-editor'),
        ], 'assets');

        // Publish stubs
        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/ai-text-editor'),
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ai-text-editor');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function loadRoutes(): void
    {
        // Installer routes
        Route::prefix('ai-editor')
            ->middleware(['web'])
            ->group(function () {
                Route::get('/installer', [InstallerController::class, 'index'])->name('ai-editor.installer');
                Route::post('/installer/select-stack', [InstallerController::class, 'selectStack'])->name('ai-editor.select-stack');
                Route::post('/installer/install', [InstallerController::class, 'install'])->name('ai-editor.install');
                Route::get('/installer/progress/{id}', [InstallerController::class, 'progress'])->name('ai-editor.progress');
                Route::get('/installer/complete', [InstallerController::class, 'complete'])->name('ai-editor.complete');
            });

        // Stack management routes
        Route::prefix('ai-editor/stacks')
            ->middleware(['web', 'auth'])
            ->group(function () {
                Route::get('/', [StackController::class, 'index'])->name('ai-editor.stacks.index');
                Route::post('/switch', [StackController::class, 'switch'])->name('ai-editor.stacks.switch');
                Route::get('/status', [StackController::class, 'status'])->name('ai-editor.stacks.status');
            });

        // Theme management routes
        Route::prefix('ai-editor/themes')
            ->middleware(['web', 'auth'])
            ->group(function () {
                Route::get('/', [ThemeController::class, 'index'])->name('ai-editor.themes.index');
                Route::post('/apply', [ThemeController::class, 'apply'])->name('ai-editor.themes.apply');
                Route::get('/preview/{theme}', [ThemeController::class, 'preview'])->name('ai-editor.themes.preview');
            });
    }
}