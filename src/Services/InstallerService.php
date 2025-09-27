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

        $totalSteps = 9;
        $currentStep = 0;

        try {
            $this->log("🚀 Starting installation for stack: {$stack}");
            $this->log("📊 Progress: 0/{$totalSteps} (0%)");
            
            // Validate stack
            if (!$this->stackService->isValidStack($stack)) {
                throw new \InvalidArgumentException("Invalid stack: {$stack}");
            }
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Stack validated");

            // Install dependencies
            $this->log("📦 Installing dependencies...");
            $this->installDependencies();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Dependencies installed");

            // Generate scaffolding
            $this->log("🏗️  Generating scaffolding...");
            $this->generateScaffolding();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Scaffolding generated");

            // Setup authentication
            $this->log("🔐 Setting up authentication...");
            $this->setupAuthentication();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Authentication setup");

            // Setup theme
            $this->log("🎨 Setting up theme...");
            $this->setupTheme();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Theme applied");

            // Create starter pages
            $this->log("📄 Creating starter pages...");
            $this->createStarterPages();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Starter pages created");

            // Setup routes
            $this->log("🛣️  Setting up routes...");
            $this->setupRoutes();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Routes configured");

            // Run migrations
            $this->log("🗄️  Running migrations...");
            $this->runMigrations();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Migrations completed");

            // Seed database
            $this->log("🌱 Seeding database...");
            $this->seedDatabase();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Database seeded");

            // Create admin user
            $this->log("👤 Creating admin user...");
            $this->createAdminUser();
            $this->updateProgress(++$currentStep, $totalSteps, "✅ Admin user created");

            $this->log("🎉 Installation completed successfully!");
            $this->log("📊 Final Progress: {$totalSteps}/{$totalSteps} (100%)");

            return [
                'success' => true,
                'stack' => $stack,
                'theme' => $theme,
                'logs' => $this->logs,
                'next_steps' => $this->getNextSteps()
            ];

        } catch (\Exception $e) {
            $this->log("❌ Installation failed: " . $e->getMessage(), 'error');
            $this->log("📊 Progress stopped at: {$currentStep}/{$totalSteps} (" . round(($currentStep / $totalSteps) * 100) . "%)");
            
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
            try {
                $this->log("Installing NPM dependencies: " . implode(', ', $dependencies));
                
                // Check if npm is available
                $npmCheck = Process::run("npm --version");
                if ($npmCheck->failed()) {
                    $this->log("⚠️  NPM is not available. Skipping NPM dependencies.", 'warning');
                    $this->log("You can manually install them later with: npm install " . implode(' ', $dependencies));
                    return;
                }
                
                // Check if package.json exists
                if (!File::exists(base_path('package.json'))) {
                    $this->log("⚠️  No package.json found. Creating basic package.json...");
                    $this->createBasicPackageJson();
                }
                
                $result = Process::run("npm install " . implode(' ', $dependencies));
                
                if ($result->failed()) {
                    $this->log("⚠️  NPM installation failed: " . $result->errorOutput(), 'warning');
                    $this->log("You can manually install dependencies later with: npm install " . implode(' ', $dependencies));
                    $this->log("Or skip NPM dependencies entirely if not needed for your stack.");
                } else {
                    $this->log("✅ NPM dependencies installed successfully");
                }
            } catch (\Exception $e) {
                $this->log("⚠️  NPM installation failed: " . $e->getMessage(), 'warning');
                $this->log("You can manually install dependencies later with: npm install " . implode(' ', $dependencies));
            }
        }
    }

    protected function createBasicPackageJson(): void
    {
        $packageJson = [
            'name' => 'ai-text-editor-app',
            'version' => '1.0.0',
            'description' => 'AI Text Editor Application',
            'private' => true,
            'scripts' => [
                'dev' => 'vite',
                'build' => 'vite build',
                'watch' => 'vite build --watch'
            ],
            'devDependencies' => [
                'vite' => '^5.0',
                'laravel-vite-plugin' => '^1.0'
            ]
        ];

        File::put(base_path('package.json'), json_encode($packageJson, JSON_PRETTY_PRINT));
        $this->log("Created basic package.json");
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
            try {
                $this->log("Setting up authentication...");

                // Install Laravel Breeze
                $exitCode = Artisan::call('breeze:install', [
                    '--dark' => false,
                    '--pest' => false,
                    '--ssr' => false,
                    '--typescript' => false,
                ]);

                if ($exitCode === 0) {
                    $this->log("✅ Authentication scaffolding installed successfully");
                } else {
                    $this->log("⚠️  Authentication setup completed with warnings");
                }
            } catch (\Exception $e) {
                $this->log("⚠️  Authentication setup failed: " . $e->getMessage(), 'warning');
                $this->log("You can manually run: php artisan breeze:install");
            }
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
            try {
                $this->log("Running migrations...");
                $exitCode = Artisan::call('migrate');
                
                if ($exitCode === 0) {
                    $this->log("✅ Migrations completed successfully");
                } else {
                    $this->log("⚠️  Migrations completed with warnings");
                }
            } catch (\Exception $e) {
                $this->log("⚠️  Migration failed: " . $e->getMessage(), 'warning');
                $this->log("You can manually run: php artisan migrate");
            }
        }
    }

    protected function seedDatabase(): void
    {
        if ($this->options['seed_database'] ?? true) {
            try {
                $this->log("Seeding database...");
                $exitCode = Artisan::call('db:seed');
                
                if ($exitCode === 0) {
                    $this->log("✅ Database seeded successfully");
                } else {
                    $this->log("⚠️  Database seeding completed with warnings");
                }
            } catch (\Exception $e) {
                $this->log("⚠️  Database seeding failed: " . $e->getMessage(), 'warning');
                $this->log("You can manually run: php artisan db:seed");
            }
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

    protected function updateProgress(int $current, int $total, string $message): void
    {
        $percentage = round(($current / $total) * 100);
        $this->log("📊 Progress: {$current}/{$total} ({$percentage}%) - {$message}");
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