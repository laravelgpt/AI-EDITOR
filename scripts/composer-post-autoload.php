<?php

/**
 * Composer Post-Autoload Script for Laravel 12 Compatibility
 * 
 * This script handles the post-autoload-dump event and ensures Laravel 12 compatibility
 * before running any artisan commands.
 */

// Check if we're in a Laravel application
if (!file_exists(__DIR__ . '/../artisan') || !file_exists(__DIR__ . '/../bootstrap/app.php')) {
    echo "Not in a Laravel application, skipping Laravel 12 compatibility fix.\n";
    exit(0);
}

// Check if vendor directory exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "Composer dependencies not installed, skipping Laravel 12 compatibility fix.\n";
    exit(0);
}

// Detect Laravel version
$laravelVersion = null;
$composerJsonPath = __DIR__ . '/../composer.json';

if (file_exists($composerJsonPath)) {
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    
    // Check for Laravel framework version
    if (isset($composerJson['require']['laravel/framework'])) {
        $version = $composerJson['require']['laravel/framework'];
        $version = preg_replace('/[^\d.]/', '', $version);
        if (version_compare($version, '12.0.0', '>=')) {
            $laravelVersion = $version;
        }
    }
    
    // Also check for Laravel in require-dev
    if (!$laravelVersion && isset($composerJson['require-dev']['laravel/framework'])) {
        $version = $composerJson['require-dev']['laravel/framework'];
        $version = preg_replace('/[^\d.]/', '', $version);
        if (version_compare($version, '12.0.0', '>=')) {
            $laravelVersion = $version;
        }
    }
}

if (!$laravelVersion) {
    echo "Laravel 12+ not detected, running standard package discovery...\n";
    
    // Run standard package discovery
    $output = [];
    $returnCode = 0;
    exec('php artisan package:discover --ansi 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "Package discovery completed successfully.\n";
        exit(0);
    } else {
        echo "Package discovery failed, but continuing...\n";
        exit(0);
    }
}

echo "Laravel 12+ detected (version: {$laravelVersion}), applying compatibility fixes...\n";

// Apply Laravel 12 fixes immediately
$artisanPath = __DIR__ . '/../artisan';
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

// Update artisan file
if (file_put_contents($artisanPath, $artisanContent)) {
    echo "✅ Updated artisan file for Laravel 12 compatibility\n";
} else {
    echo "❌ Failed to update artisan file\n";
    exit(1);
}

// Update bootstrap/app.php
$bootstrapPath = __DIR__ . '/../bootstrap/app.php';
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

if (file_put_contents($bootstrapPath, $bootstrapContent)) {
    echo "✅ Updated bootstrap/app.php for Laravel 12 compatibility\n";
} else {
    echo "❌ Failed to update bootstrap/app.php\n";
    exit(1);
}

// Now run package discovery
echo "Running package discovery with Laravel 12 compatibility...\n";

$output = [];
$returnCode = 0;
exec('php artisan package:discover --ansi 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Package discovery completed successfully!\n";
    exit(0);
} else {
    echo "⚠️  Package discovery failed, but Laravel 12 compatibility fixes have been applied.\n";
    echo "You can now run 'php artisan package:discover' manually.\n";
    exit(0);
}
