<?php

namespace AiEditor\AiTextEditor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use AiEditor\AiTextEditor\Services\AiService;
use AiEditor\AiTextEditor\Services\MemoryService;
use AiEditor\AiTextEditor\Http\Controllers\AiController;
use AiEditor\AiTextEditor\Http\Controllers\MemoryController;

class AiTextEditorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-text-editor.php', 'ai-text-editor');
        
        $this->app->singleton(AiService::class);
        $this->app->singleton(MemoryService::class);
    }

    public function boot(): void
    {
        // Check Laravel 12 compatibility
        $this->checkLaravel12Compatibility();

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/ai-text-editor.php' => config_path('ai-text-editor.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/ai-text-editor'),
        ], 'views');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/ai-text-editor'),
        ], 'assets');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ai-text-editor');

        // Load routes
        $this->loadRoutes();

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \AiEditor\AiTextEditor\Console\Commands\InstallCommand::class,
                \AiEditor\AiTextEditor\Console\Commands\StackCommand::class,
                \AiEditor\AiTextEditor\Console\Commands\ThemeCommand::class,
                \AiEditor\AiTextEditor\Console\Commands\AiFeatureCommand::class,
                \AiEditor\AiTextEditor\Console\Commands\WebInstallerCommand::class,
                \AiEditor\AiTextEditor\Console\Commands\FixLaravel12Command::class,
            ]);
        }
    }

    protected function checkLaravel12Compatibility(): void
    {
        try {
            $laravelVersion = app()->version();
            if (version_compare($laravelVersion, '12.0.0', '>=')) {
                // Check if artisan file needs updating
                $artisanPath = base_path('artisan');
                if (file_exists($artisanPath)) {
                    $artisanContent = file_get_contents($artisanPath);
                    if (strpos($artisanContent, 'handleCommand') !== false) {
                        // Laravel 12 compatibility issue detected
                        $this->app['log']->warning('Laravel 12 compatibility issue detected. Run "php artisan ai-editor:fix-laravel12" to fix.');
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently handle any errors during compatibility check
        }
    }

    protected function loadRoutes(): void
    {
        Route::prefix('ai-editor')
            ->middleware(['web', 'auth'])
            ->group(function () {
                Route::post('/generate', [AiController::class, 'generate'])->name('ai-editor.generate');
                Route::post('/edit', [AiController::class, 'edit'])->name('ai-editor.edit');
                Route::post('/summarize', [AiController::class, 'summarize'])->name('ai-editor.summarize');
                Route::post('/complete', [AiController::class, 'complete'])->name('ai-editor.complete');
                
                Route::get('/memory', [MemoryController::class, 'index'])->name('ai-editor.memory.index');
                Route::post('/memory', [MemoryController::class, 'store'])->name('ai-editor.memory.store');
                Route::get('/memory/{id}', [MemoryController::class, 'show'])->name('ai-editor.memory.show');
                Route::post('/memory/{id}/restore', [MemoryController::class, 'restore'])->name('ai-editor.memory.restore');
                Route::delete('/memory/{id}', [MemoryController::class, 'destroy'])->name('ai-editor.memory.destroy');
            });
    }
}
