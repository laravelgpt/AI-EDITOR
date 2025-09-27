# Quick Installation Guide - Laravel 12 Fix

## Problem
If you get this error during installation:
```
Fatal error: Call to undefined method Illuminate\Foundation\Configuration\ApplicationBuilder::handleCommand()
```

## Solution

### Step 1: Fix Laravel 12 Compatibility (BEFORE installing the package)

```bash
# Download the fix script
curl -o pre-install-laravel12-fix.php https://raw.githubusercontent.com/laravelgpt/AI-EDITOR/main/scripts/pre-install-laravel12-fix.php

# Run the fix
php pre-install-laravel12-fix.php
```

### Step 2: Install the Package

```bash
composer require ai-editor/ai-text-editor
```

### Step 3: Run Package Discovery

```bash
php artisan package:discover
```

### Step 4: Install the AI Text Editor

```bash
php artisan ai-editor:install
```

## Alternative: Manual Fix

If the script doesn't work, manually update these files:

### 1. Update `artisan` file

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

### 2. Update `bootstrap/app.php` file

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

### 3. Install the Package

```bash
composer require ai-editor/ai-text-editor
php artisan package:discover
php artisan ai-editor:install
```

## Verification

After installation, verify everything works:

```bash
php artisan --version
php artisan list | grep ai-editor
php artisan ai-editor:install
```

## Support

If you still have issues:

1. **Check Laravel Version**: `php artisan --version`
2. **Clear Caches**: `php artisan config:clear && php artisan cache:clear`
3. **Regenerate Autoloader**: `composer dump-autoload`
4. **Check PHP Version**: `php --version` (should be 8.4+)

## Package Features

Once installed, you get:

- ✅ **Multi-Stack Support**: Blade+Livewire, Vue.js SPA, React+Next.js
- ✅ **AI-Powered Features**: Dynamic feature generation
- ✅ **Interactive Installer**: CLI and web-based installation
- ✅ **Theme System**: Multiple built-in themes
- ✅ **500+ Dynamic Features**: Extensive feature library
- ✅ **Modern UI**: Beautiful, responsive interface
- ✅ **Production Ready**: Fully tested and optimized
