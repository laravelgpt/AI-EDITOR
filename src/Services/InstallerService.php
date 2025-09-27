<?php

namespace AiEditor\AiTextEditor\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use AiEditor\AiTextEditor\Services\StackService;
use AiEditor\AiTextEditor\Services\ThemeService;

class InstallerService
{
    protected StackService $stackService;
    protected ThemeService $themeService;
    protected array $logs = [];
    protected string $selectedStack = '';
    protected string $selectedTheme = '';
    protected array $options = [];

    public function __construct(StackService $stackService, ThemeService $themeService)
    {
        $this->stackService = $stackService;
        $this->themeService = $themeService;
    }

    public function install(string $stack, string $theme = 'default', array $options = []): array
    {
        $this->selectedStack = $stack;
        $this->selectedTheme = $theme;
        $this->options = $options;
        $this->logs = [];

        try {
            $this->log("Starting installation for stack: {$stack}");
            
            // Validate stack
            if (!$this->stackService->isValidStack($stack)) {
                throw new \InvalidArgumentException("Invalid stack: {$stack}");
            }

            // Install dependencies
            $this->installDependencies();

            // Generate scaffolding
            $this->generateScaffolding();

            // Setup authentication
            $this->setupAuthentication();

            // Setup theme
            $this->setupTheme();

            // Create starter pages
            $this->createStarterPages();

            // Setup routes
            $this->setupRoutes();

            // Run migrations
            $this->runMigrations();

            // Seed database
            $this->seedDatabase();

            // Create admin user
            $this->createAdminUser();

            $this->log("Installation completed successfully!");

            return [
                'success' => true,
                'stack' => $stack,
                'theme' => $theme,
                'logs' => $this->logs,
                'next_steps' => $this->getNextSteps()
            ];

        } catch (\Exception $e) {
            $this->log("Installation failed: " . $e->getMessage(), 'error');
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'logs' => $this->logs
            ];
        }
    }

    protected function installDependencies(): void
    {
        $this->log("Installing dependencies...");

        // Install Composer dependencies
        if ($this->options['install_composer'] ?? true) {
            $this->installComposerDependencies();
        }

        // Install NPM dependencies
        if ($this->options['install_npm'] ?? true) {
            $this->installNpmDependencies();
        }
    }

    protected function installComposerDependencies(): void
    {
        // Fix Laravel 12 compatibility issues first
        $this->fixLaravel12Compatibility();

        $dependencies = $this->stackService->getComposerDependencies($this->selectedStack);
        
        if (!empty($dependencies)) {
            $this->log("Installing Composer dependencies: " . implode(', ', $dependencies));
            
            $result = Process::run("composer require " . implode(' ', $dependencies));
            
            if ($result->failed()) {
                throw new \Exception("Failed to install Composer dependencies: " . $result->errorOutput());
            }
        }
    }

    protected function fixLaravel12Compatibility(): void
    {
        $this->log('Checking Laravel 12 compatibility...');

        // Check if we're running Laravel 12+
        $laravelVersion = app()->version();
        if (version_compare($laravelVersion, '12.0.0', '>=')) {
            $this->log('Detected Laravel 12+, applying compatibility fixes...');

            // Update artisan file for Laravel 12
            $this->updateArtisanFile();

            // Update bootstrap/app.php for Laravel 12
            $this->updateBootstrapApp();
        }
    }

    protected function updateArtisanFile(): void
    {
        $artisanPath = base_path('artisan');
        $stubPath = __DIR__ . '/../../stubs/laravel-12-artisan.stub';

        if (File::exists($stubPath)) {
            File::copy($stubPath, $artisanPath);
            $this->log('Updated artisan file for Laravel 12 compatibility');
        }
    }

    protected function updateBootstrapApp(): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');
        $stubPath = __DIR__ . '/../../stubs/laravel-12-bootstrap-app.stub';

        if (File::exists($stubPath)) {
            File::copy($stubPath, $bootstrapPath);
            $this->log('Updated bootstrap/app.php for Laravel 12 compatibility');
        }
    }

    protected function installNpmDependencies(): void
    {
        $dependencies = $this->stackService->getNpmDependencies($this->selectedStack);
        
        if (!empty($dependencies)) {
            $this->log("Installing NPM dependencies: " . implode(', ', $dependencies));
            
            $result = Process::run("npm install " . implode(' ', $dependencies));
            
            if ($result->failed()) {
                throw new \Exception("Failed to install NPM dependencies: " . $result->errorOutput());
            }
        }
    }

    protected function generateScaffolding(): void
    {
        $this->log("Generating scaffolding for {$this->selectedStack}...");

        // Copy stack-specific files
        $this->stackService->copyStackFiles($this->selectedStack);

        // Generate configuration files
        $this->generateConfigFiles();

        // Setup build tools
        $this->setupBuildTools();
    }

    protected function generateConfigFiles(): void
    {
        $this->log("Generating configuration files...");

        // Generate package.json if needed
        if ($this->selectedStack !== 'blade-livewire') {
            $this->generatePackageJson();
        }

        // Generate Vite config
        $this->generateViteConfig();

        // Generate Tailwind config
        $this->generateTailwindConfig();

        // Generate PostCSS config
        $this->generatePostCssConfig();
    }

    protected function generatePackageJson(): void
    {
        $packageJson = [
            'name' => 'ai-text-editor',
            'private' => true,
            'type' => 'module',
            'scripts' => $this->stackService->getPackageScripts($this->selectedStack),
            'dependencies' => $this->stackService->getNpmDependencies($this->selectedStack, true),
            'devDependencies' => [
                'vite' => '^5.0.0',
                'laravel-vite-plugin' => '^1.0.0',
                'autoprefixer' => '^10.4.0',
                'postcss' => '^8.4.0',
            ]
        ];

        File::put(base_path('package.json'), json_encode($packageJson, JSON_PRETTY_PRINT));
        $this->log("Generated package.json");
    }

    protected function generateViteConfig(): void
    {
        $viteConfig = $this->stackService->getViteConfig($this->selectedStack);
        File::put(base_path('vite.config.js'), $viteConfig);
        $this->log("Generated vite.config.js");
    }

    protected function generateTailwindConfig(): void
    {
        $tailwindConfig = $this->stackService->getTailwindConfig();
        File::put(base_path('tailwind.config.js'), $tailwindConfig);
        $this->log("Generated tailwind.config.js");
    }

    protected function generatePostCssConfig(): void
    {
        $postcssConfig = "module.exports = {\n  plugins: {\n    tailwindcss: {},\n    autoprefixer: {},\n  },\n}\n";
        File::put(base_path('postcss.config.js'), $postcssConfig);
        $this->log("Generated postcss.config.js");
    }

    protected function setupBuildTools(): void
    {
        $this->log("Setting up build tools...");

        // Copy CSS files
        $this->copyCssFiles();

        // Copy JavaScript files
        $this->copyJsFiles();
    }

    protected function copyCssFiles(): void
    {
        $cssPath = resource_path('css');
        
        if (!File::exists($cssPath)) {
            File::makeDirectory($cssPath, 0755, true);
        }

        // Copy app.css
        File::copy(
            __DIR__ . '/../../stubs/css/app.css',
            $cssPath . '/app.css'
        );

        // Copy stack-specific CSS
        $stackCssPath = __DIR__ . "/../../stubs/stacks/{$this->selectedStack}/css";
        if (File::exists($stackCssPath)) {
            File::copyDirectory($stackCssPath, $cssPath);
        }
    }

    protected function copyJsFiles(): void
    {
        $jsPath = resource_path('js');
        
        if (!File::exists($jsPath)) {
            File::makeDirectory($jsPath, 0755, true);
        }

        // Copy stack-specific JS
        $stackJsPath = __DIR__ . "/../../stubs/stacks/{$this->selectedStack}/js";
        if (File::exists($stackJsPath)) {
            File::copyDirectory($stackJsPath, $jsPath);
        }
    }

    protected function setupAuthentication(): void
    {
        if ($this->options['setup_authentication'] ?? true) {
            $this->log("Setting up authentication...");

            // Install Laravel Breeze
            Artisan::call('breeze:install', [
                '--stack' => 'blade',
                '--dark' => false,
                '--pest' => false,
                '--ssr' => false,
                '--typescript' => false,
            ]);

            $this->log("Authentication scaffolding installed");
        }
    }

    protected function setupTheme(): void
    {
        if ($this->options['setup_theme'] ?? true) {
            $this->log("Setting up theme: {$this->selectedTheme}");

            $this->themeService->applyTheme($this->selectedTheme);
            $this->log("Theme applied successfully");
        }
    }

    protected function createStarterPages(): void
    {
        $this->log("Creating starter pages...");

        $this->stackService->createStarterPages($this->selectedStack);
    }

    protected function setupRoutes(): void
    {
        $this->log("Setting up routes...");

        $this->stackService->setupRoutes($this->selectedStack);
    }

    protected function runMigrations(): void
    {
        if ($this->options['run_migrations'] ?? true) {
            $this->log("Running migrations...");
            Artisan::call('migrate');
            $this->log("Migrations completed");
        }
    }

    protected function seedDatabase(): void
    {
        if ($this->options['seed_database'] ?? true) {
            $this->log("Seeding database...");
            Artisan::call('db:seed');
            $this->log("Database seeded");
        }
    }

    protected function createAdminUser(): void
    {
        if ($this->options['create_admin_user'] ?? true) {
            $this->log("Creating admin user...");

            // Create admin user
            $admin = \App\Models\User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            // Assign admin role
            $admin->assignRole('admin');

            $this->log("Admin user created: admin@example.com / password");
        }
    }

    protected function getNextSteps(): array
    {
        $steps = [
            "Your {$this->selectedStack} starter kit has been installed successfully!",
            "",
            "Next steps:",
            "1. Run 'php artisan serve' to start the development server",
        ];

        if ($this->selectedStack !== 'blade-livewire') {
            $steps[] = "2. Run 'npm run dev' to start the frontend build process";
            $steps[] = "3. Visit http://localhost:8000 to see your application";
        } else {
            $steps[] = "2. Visit http://localhost:8000 to see your application";
        }

        $steps[] = "";
        $steps[] = "Admin credentials:";
        $steps[] = "Email: admin@example.com";
        $steps[] = "Password: password";
        $steps[] = "";
        $steps[] = "For more information, visit: https://github.com/laravelgpt/AI-EDITOR";

        return $steps;
    }

    protected function log(string $message, string $level = 'info'): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $this->logs[] = "[{$timestamp}] [{$level}] {$message}";
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function getAvailableStacks(): array
    {
        return $this->stackService->getAvailableStacks();
    }

    public function getAvailableThemes(): array
    {
        return $this->themeService->getAvailableThemes();
    }
}