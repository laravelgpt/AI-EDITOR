@echo off
echo AI Text Editor - Laravel 12 Compatibility Fix
echo ================================================
echo.

REM Check if we're in a Laravel application
if not exist "artisan" (
    echo ERROR: Not in a Laravel application directory.
    echo Please run this script from your Laravel application root.
    pause
    exit /b 1
)

if not exist "bootstrap\app.php" (
    echo ERROR: Not in a Laravel application directory.
    echo Please run this script from your Laravel application root.
    pause
    exit /b 1
)

REM Check if vendor directory exists
if not exist "vendor\autoload.php" (
    echo ERROR: Composer dependencies not installed.
    echo Please run 'composer install' first.
    pause
    exit /b 1
)

echo Laravel application detected.
echo.

REM Run the PHP fix script
php scripts\install-laravel12-fix.php

echo.
echo Press any key to continue...
pause >nul
