# AI Text Editor - Installation Guide

## Laravel 12 Compatibility Issues

If you encounter the error:
```
Fatal error: Uncaught Error: Call to undefined method Illuminate\Foundation\Configuration\ApplicationBuilder::handleCommand()
```

This is a Laravel 12 compatibility issue. Here are the solutions:

## Solution 1: Automatic Fix (Recommended)

The package now includes automatic Laravel 12 compatibility detection and fixes. However, if you still encounter issues during Composer installation, follow these steps:

### Step 1: Install the package
```bash
composer require ai-editor/ai-text-editor
```

### Step 2: If installation fails, apply manual fixes
```bash
# Copy the Laravel 12 compatible artisan file
cp vendor/ai-editor/ai-text-editor/stubs/laravel-12-artisan.stub artisan

# Copy the Laravel 12 compatible bootstrap file
cp vendor/ai-editor/ai-text-editor/stubs/laravel-12-bootstrap-app.stub bootstrap/app.php
```

### Step 3: Run package discovery
```bash
php artisan package:discover
```

## Solution 2: Manual Installation

If the automatic installation fails:

### Step 1: Download the package manually
```bash
git clone https://github.com/laravelgpt/AI-EDITOR.git
cd AI-EDITOR
composer install
```

### Step 2: Apply Laravel 12 fixes
```bash
php scripts/fix-laravel12.php
```

### Step 3: Install in your Laravel application
```bash
# Copy the package to your Laravel app's vendor directory
cp -r . /path/to/your/laravel-app/vendor/ai-editor/ai-text-editor

# Or use the package as a local dependency
composer config repositories.ai-editor path /path/to/AI-EDITOR
composer require ai-editor/ai-text-editor
```

## Solution 3: Pre-installation Fix

If you want to fix the Laravel 12 compatibility before installing the package:

### Step 1: Update your artisan file
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

### Step 2: Update your bootstrap/app.php
Replace the contents of your `bootstrap/app.php` with:

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

### Step 3: Install the package
```bash
composer require ai-editor/ai-text-editor
```

## Verification

After installation, verify everything works:

```bash
# Check Laravel version
php artisan --version

# Check if the package is installed
php artisan list | grep ai-editor

# Run the installer
php artisan ai-editor:install
```

## Troubleshooting

### Issue: Package discovery still fails
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoloader
composer dump-autoload

# Try package discovery again
php artisan package:discover
```

### Issue: Commands not found
```bash
# Check if the service provider is registered
php artisan config:show app.providers | grep AiTextEditor

# If not found, manually register in config/app.php
# Add: AiEditor\AiTextEditor\AiTextEditorServiceProvider::class,
```

### Issue: Still getting handleCommand error
```bash
# Use the built-in fix command
php artisan ai-editor:fix-laravel12

# Or manually copy the stubs
cp vendor/ai-editor/ai-text-editor/stubs/laravel-12-artisan.stub artisan
cp vendor/ai-editor/ai-text-editor/stubs/laravel-12-bootstrap-app.stub bootstrap/app.php
```

## Support

If you continue to experience issues:

1. **Check Laravel Version**: Ensure you're using Laravel 12+
2. **Check PHP Version**: Ensure you're using PHP 8.4+
3. **Clear Caches**: Run all cache clearing commands
4. **Check Dependencies**: Ensure all Composer dependencies are properly installed
5. **Manual Fix**: Use the manual installation method if automatic fails

## Package Features

Once successfully installed, the package provides:

- ✅ **Multi-Stack Support**: Blade+Livewire, Vue.js SPA, React+Next.js
- ✅ **AI-Powered Features**: Dynamic feature generation with AI
- ✅ **Interactive Installer**: CLI and web-based installation
- ✅ **Theme System**: Multiple built-in themes
- ✅ **Laravel 12+ Support**: Full compatibility with latest Laravel
- ✅ **500+ Dynamic Features**: Extensive feature library
- ✅ **Modern UI**: Beautiful, responsive interface
- ✅ **Production Ready**: Fully tested and optimized
