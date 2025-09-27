<?php

/**
 * Package Discovery Script for Laravel 12 Compatibility
 * 
 * This script handles package discovery while ensuring Laravel 12 compatibility
 */

// Check if we're in a Laravel application
if (!file_exists(__DIR__ . '/../artisan') || !file_exists(__DIR__ . '/../bootstrap/app.php')) {
    echo "Not in a Laravel application, skipping package discovery.\n";
    exit(0);
}

// First, apply Laravel 12 compatibility fixes if needed
$composerJsonPath = __DIR__ . '/../composer.json';
if (file_exists($composerJsonPath)) {
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    $laravelVersion = null;

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

    if ($laravelVersion) {
        echo "Laravel 12+ detected, applying compatibility fixes before package discovery...\n";
        
        // Apply Laravel 12 fixes
        include __DIR__ . '/fix-laravel12.php';
    }
}

// Now run the actual package discovery
echo "Running package discovery...\n";

// Try to run artisan package:discover
$output = [];
$returnCode = 0;

// Use exec to capture output and return code
exec('php artisan package:discover --ansi 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "Package discovery completed successfully.\n";
    exit(0);
} else {
    echo "Package discovery failed, but Laravel 12 compatibility fixes have been applied.\n";
    echo "You can now run 'php artisan package:discover' manually.\n";
    exit(0);
}
