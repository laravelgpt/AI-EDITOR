<?php

/**
 * Artisan Wrapper for Laravel 12 Compatibility
 * 
 * This script provides a compatibility layer for artisan commands
 * that works with both Laravel 11 and Laravel 12
 */

// Check if we're in a Laravel application
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "Laravel application not found.\n";
    exit(1);
}

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Try to detect Laravel version and handle accordingly
try {
    // First, try to load the application
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // Check if it's a Laravel 12+ application
    if (method_exists($app, 'make')) {
        try {
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            
            $status = $kernel->handle(
                $input = new Symfony\Component\Console\Input\ArgvInput,
                new Symfony\Component\Console\Output\ConsoleOutput
            );
            
            $kernel->terminate($input, $status);
            exit($status);
        } catch (Error $e) {
            // If we get a method not found error, it's likely Laravel 12
            if (strpos($e->getMessage(), 'handleCommand') !== false) {
                echo "Laravel 12 compatibility issue detected. Applying fixes...\n";
                
                // Apply Laravel 12 fixes
                include __DIR__ . '/fix-laravel12.php';
                
                // Try again
                $app = require_once __DIR__ . '/../bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                
                $status = $kernel->handle(
                    $input = new Symfony\Component\Console\Input\ArgvInput,
                    new Symfony\Component\Console\Output\ConsoleOutput
                );
                
                $kernel->terminate($input, $status);
                exit($status);
            }
            
            throw $e;
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
