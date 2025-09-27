#!/bin/bash

echo "AI Text Editor - Laravel 12 Compatibility Fix"
echo "================================================"
echo

# Check if we're in a Laravel application
if [ ! -f "artisan" ] || [ ! -f "bootstrap/app.php" ]; then
    echo "ERROR: Not in a Laravel application directory."
    echo "Please run this script from your Laravel application root."
    exit 1
fi

# Check if vendor directory exists
if [ ! -f "vendor/autoload.php" ]; then
    echo "ERROR: Composer dependencies not installed."
    echo "Please run 'composer install' first."
    exit 1
fi

echo "Laravel application detected."
echo

# Run the PHP fix script
php scripts/install-laravel12-fix.php
