<?php

namespace AiEditor\AiTextEditor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PostInstallCommand extends Command
{
    protected $signature = 'ai-editor:post-install';
    protected $description = 'Run post-installation setup for AI Text Editor';

    public function handle(): int
    {
        $this->info('🔧 AI Text Editor - Post-Installation Setup');
        $this->info('==========================================');
        $this->newLine();

        // Check Laravel version
        $laravelVersion = app()->version();
        $this->info("Detected Laravel version: {$laravelVersion}");

        if (version_compare($laravelVersion, '12.0.0', '>=')) {
            $this->info('✅ Laravel 12+ detected, applying compatibility fixes...');
            $this->fixLaravel12Compatibility();
        } else {
            $this->info('ℹ️  Laravel 11 or earlier detected, no compatibility fixes needed.');
        }

        // Publish configuration
        $this->publishConfiguration();

        // Run package discovery
        $this->runPackageDiscovery();

        $this->newLine();
        $this->info('✅ Post-installation setup completed successfully!');
        $this->newLine();
        
        $this->info('Next steps:');
        $this->line('1. Run: php artisan ai-editor:install');
        $this->line('2. Or visit: http://your-app.com/ai-editor/installer');
        $this->newLine();

        return 0;
    }

    protected function fixLaravel12Compatibility(): void
    {
        $this->info('🔧 Applying Laravel 12 compatibility fixes...');

        // Fix artisan file
        $artisanPath = base_path('artisan');
        $artisanContent = '#!/usr/bin/env php
<?php

define(\'LARAVEL_START\', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We\'ll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.\'/vendor/autoload.php\';

$app = require_once __DIR__.\'/bootstrap/app.php\';

/*
|--------------------------------------------------------------------------
| Run The Artisan Application
|--------------------------------------------------------------------------
|
| When we run the console application, the current CLI command will be
| executed in this console and the response sent back to a terminal
| or another output device for the developers. Here goes nothing!
|
*/

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

/*
|--------------------------------------------------------------------------
| Shutdown The Application
|--------------------------------------------------------------------------
|
| Once Artisan has finished running, we will fire off the shutdown events
| so that any final work may be done by the application before we shut
| down the script. This is the last thing to happen to the request.
|
*/

$kernel->terminate($input, $status);

exit($status);
';

        if (File::put($artisanPath, $artisanContent)) {
            $this->info('✅ Updated artisan file for Laravel 12 compatibility');
        } else {
            $this->error('❌ Failed to update artisan file');
        }

        // Fix bootstrap/app.php
        $bootstrapPath = base_path('bootstrap/app.php');
        $bootstrapContent = '<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.\'/../routes/web.php\',
        api: __DIR__.\'/../routes/api.php\',
        commands: __DIR__.\'/../routes/console.php\',
        health: \'/up\',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
';

        if (File::put($bootstrapPath, $bootstrapContent)) {
            $this->info('✅ Updated bootstrap/app.php for Laravel 12 compatibility');
        } else {
            $this->error('❌ Failed to update bootstrap/app.php');
        }
    }

    protected function publishConfiguration(): void
    {
        $this->info('📦 Publishing configuration...');
        
        try {
            $this->call('vendor:publish', [
                '--provider' => 'AiEditor\\AiTextEditor\\AiTextEditorServiceProvider',
                '--tag' => 'config'
            ]);
            $this->info('✅ Configuration published successfully');
        } catch (\Exception $e) {
            $this->warn('⚠️  Failed to publish configuration: ' . $e->getMessage());
        }
    }

    protected function runPackageDiscovery(): void
    {
        $this->info('🔍 Running package discovery...');
        
        try {
            $this->call('package:discover');
            $this->info('✅ Package discovery completed successfully');
        } catch (\Exception $e) {
            $this->warn('⚠️  Package discovery failed: ' . $e->getMessage());
            $this->info('You can run "php artisan package:discover" manually later.');
        }
    }
}
