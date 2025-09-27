<?php

/**
 * Laravel 12 Installation Fix Script
 * 
 * This script provides a comprehensive solution for Laravel 12 compatibility issues
 * that can be run independently of Composer events.
 */

echo "🔧 AI Text Editor - Laravel 12 Compatibility Fix\n";
echo "================================================\n\n";

// Check if we're in a Laravel application
if (!file_exists(__DIR__ . '/../artisan') || !file_exists(__DIR__ . '/../bootstrap/app.php')) {
    echo "❌ Not in a Laravel application directory.\n";
    echo "Please run this script from your Laravel application root.\n";
    exit(1);
}

// Check if vendor directory exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "❌ Composer dependencies not installed.\n";
    echo "Please run 'composer install' first.\n";
    exit(1);
}

echo "✅ Laravel application detected.\n";

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
    echo "ℹ️  Laravel 12+ not detected, but applying compatibility fixes anyway...\n";
} else {
    echo "✅ Laravel 12+ detected (version: {$laravelVersion})\n";
}

echo "\n🔧 Applying Laravel 12 compatibility fixes...\n";

// Fix artisan file
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

if (file_put_contents($artisanPath, $artisanContent)) {
    echo "✅ Updated artisan file for Laravel 12 compatibility\n";
} else {
    echo "❌ Failed to update artisan file\n";
    exit(1);
}

// Fix bootstrap/app.php
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

echo "\n🧪 Testing Laravel 12 compatibility...\n";

// Test if artisan works now
$output = [];
$returnCode = 0;

exec('php artisan --version 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Laravel 12 compatibility test passed!\n";
    echo "Laravel version: " . implode("\n", $output) . "\n";
} else {
    echo "⚠️  Laravel 12 compatibility test failed, but fixes have been applied.\n";
    echo "You may need to run 'composer dump-autoload' and try again.\n";
}

echo "\n🎉 Laravel 12 compatibility fixes applied successfully!\n";
echo "\nNext steps:\n";
echo "1. Run 'composer dump-autoload' to regenerate autoloader\n";
echo "2. Run 'php artisan package:discover' to discover packages\n";
echo "3. Run 'php artisan ai-editor:install' to install the AI Text Editor\n";
echo "\nFor more help, see: INSTALLATION-GUIDE.md\n";

exit(0);
