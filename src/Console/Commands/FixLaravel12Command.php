<?php

namespace AiEditor\AiTextEditor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixLaravel12Command extends Command
{
    protected $signature = 'ai-editor:fix-laravel12';
    protected $description = 'Fix Laravel 12 compatibility issues';

    public function handle(): int
    {
        $this->info('🔧 Fixing Laravel 12 compatibility issues...');

        // Check Laravel version
        $laravelVersion = app()->version();
        $this->info("Detected Laravel version: {$laravelVersion}");

        if (version_compare($laravelVersion, '12.0.0', '>=')) {
            $this->info('✅ Laravel 12+ detected, applying fixes...');

            // Fix artisan file
            $this->fixArtisanFile();

            // Fix bootstrap/app.php
            $this->fixBootstrapApp();

            $this->info('✅ Laravel 12 compatibility fixes applied successfully!');
            return 0;
        } else {
            $this->info('ℹ️  Laravel 12+ not detected, no fixes needed.');
            return 0;
        }
    }

    protected function fixArtisanFile(): void
    {
        $artisanPath = base_path('artisan');
        $stubPath = __DIR__ . '/../../../stubs/laravel-12-artisan.stub';

        if (File::exists($stubPath)) {
            File::copy($stubPath, $artisanPath);
            $this->info('✅ Updated artisan file for Laravel 12 compatibility');
        } else {
            $this->warn('⚠️  Laravel 12 artisan stub not found');
        }
    }

    protected function fixBootstrapApp(): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');
        $stubPath = __DIR__ . '/../../../stubs/laravel-12-bootstrap-app.stub';

        if (File::exists($stubPath)) {
            File::copy($stubPath, $bootstrapPath);
            $this->info('✅ Updated bootstrap/app.php for Laravel 12 compatibility');
        } else {
            $this->warn('⚠️  Laravel 12 bootstrap stub not found');
        }
    }
}
