<?php

namespace LaravelStarterKit\MultiStack\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class WebInstallerCommand extends Command
{
    protected $signature = 'multi-stack:web-installer 
                            {--port=8001 : Port for the web installer}
                            {--host=127.0.0.1 : Host for the web installer}';

    protected $description = 'Start the web-based installer for Laravel Multi-Stack Starter Kit';

    public function handle(): int
    {
        $this->info('🌐 Starting Laravel Multi-Stack Web Installer...');
        $this->newLine();

        $host = $this->option('host');
        $port = $this->option('port');

        $this->info("Web installer will be available at: http://{$host}:{$port}/multi-stack/installer");
        $this->newLine();

        $this->info('Press Ctrl+C to stop the installer');
        $this->newLine();

        // Start the development server
        Artisan::call('serve', [
            '--host' => $host,
            '--port' => $port,
        ]);

        return 0;
    }
}