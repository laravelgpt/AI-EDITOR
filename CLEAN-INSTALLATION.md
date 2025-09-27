# Clean Installation Guide - AI Text Editor

## Simple Installation Process

### Step 1: Install the Package

```bash
composer require ai-editor/ai-text-editor
```

### Step 2: Run Post-Installation Setup

```bash
php artisan ai-editor:post-install
```

This command will:
- ✅ Detect Laravel 12+ and apply compatibility fixes automatically
- ✅ Publish configuration files
- ✅ Run package discovery
- ✅ Set up everything needed for the package

### Step 3: Install AI Text Editor

```bash
php artisan ai-editor:install
```

## What This Solves

The `ai-editor:post-install` command automatically handles:

1. **Laravel 12 Compatibility**: Detects Laravel 12+ and fixes the `handleCommand()` error
2. **Configuration**: Publishes all necessary configuration files
3. **Package Discovery**: Runs package discovery safely
4. **Setup**: Prepares everything for the main installation

## Manual Laravel 12 Fix (If Needed)

If you still encounter the `handleCommand()` error, you can manually fix it:

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

### 3. Run Package Discovery

```bash
php artisan package:discover
```

### 4. Run Post-Installation Setup

```bash
php artisan ai-editor:post-install
```

## Verification

After installation, verify everything works:

```bash
# Check Laravel version
php artisan --version

# Check if commands are available
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

# Run post-installation setup
php artisan ai-editor:post-install
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

# Or run post-installation setup
php artisan ai-editor:post-install
```

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

## Support

If you continue to experience issues:

1. **Check Laravel Version**: Ensure you're using Laravel 12+
2. **Check PHP Version**: Ensure you're using PHP 8.4+
3. **Clear Caches**: Run all cache clearing commands
4. **Check Dependencies**: Ensure all Composer dependencies are properly installed
5. **Run Post-Installation Setup**: Use `php artisan ai-editor:post-install`
