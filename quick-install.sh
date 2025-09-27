#!/bin/bash

echo "AI Text Editor - Quick Installation for Laravel 12"
echo "=================================================="
echo

# Check if we're in a Laravel application
if [ ! -f "artisan" ] || [ ! -f "bootstrap/app.php" ]; then
    echo "ERROR: Not in a Laravel application directory."
    echo "Please run this script from your Laravel application root."
    exit 1
fi

echo "Laravel application detected."
echo

echo "Step 1: Downloading Laravel 12 fix script..."
curl -o pre-install-laravel12-fix.php https://raw.githubusercontent.com/laravelgpt/AI-EDITOR/main/scripts/pre-install-laravel12-fix.php

if [ ! -f "pre-install-laravel12-fix.php" ]; then
    echo "ERROR: Failed to download fix script."
    echo "Please download manually from: https://raw.githubusercontent.com/laravelgpt/AI-EDITOR/main/scripts/pre-install-laravel12-fix.php"
    exit 1
fi

echo "Step 2: Applying Laravel 12 compatibility fixes..."
php pre-install-laravel12-fix.php

if [ $? -ne 0 ]; then
    echo "ERROR: Laravel 12 fix failed."
    echo "Please check the error messages above."
    exit 1
fi

echo
echo "Step 3: Installing AI Text Editor package..."
composer require ai-editor/ai-text-editor

if [ $? -ne 0 ]; then
    echo "ERROR: Package installation failed."
    echo "Please check the error messages above."
    exit 1
fi

echo
echo "Step 4: Running package discovery..."
php artisan package:discover

echo
echo "Step 5: Installing AI Text Editor..."
php artisan ai-editor:install

echo
echo "✅ Installation completed successfully!"
echo
echo "Next steps:"
echo "1. Visit your application in the browser"
echo "2. Run 'php artisan ai-editor:web-installer' for web-based installation"
echo "3. Check the documentation for more features"
echo
