# Laravel 12 Compatibility Fix

## Problem

When installing the AI Text Editor package in Laravel 12+, you may encounter the following error:

```
Fatal error: Uncaught Error: Call to undefined method Illuminate\Foundation\Configuration\ApplicationBuilder::handleCommand() in artisan:16
```

This happens because Laravel 12 changed the application structure and the `handleCommand()` method was removed.

## Solution

### Automatic Fix (Recommended)

Run the built-in Laravel 12 compatibility fix command:

```bash
php artisan ai-editor:fix-laravel12
```

This command will:
- Detect if you're running Laravel 12+
- Update your `artisan` file to be compatible with Laravel 12
- Update your `bootstrap/app.php` file to use the new Laravel 12 structure

### Manual Fix

If the automatic fix doesn't work, you can manually update the files:

#### 1. Update `artisan` file

Replace the contents of your `artisan` file with:

```php
#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

exit($status);
```

#### 2. Update `bootstrap/app.php` file

Replace the contents of your `bootstrap/app.php` file with:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

## Verification

After applying the fix, verify that everything works:

```bash
php artisan --version
php artisan ai-editor:install
```

## Package Installation

The package automatically applies Laravel 12 compatibility fixes during installation when Laravel 12+ is detected.

## Support

If you continue to experience issues, please:

1. Check that you're using the latest version of the package
2. Ensure your Laravel application is properly configured
3. Run `composer dump-autoload` after applying fixes
4. Clear any cached configurations with `php artisan config:clear`
