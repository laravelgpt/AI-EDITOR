<?php

namespace AiEditor\AiTextEditor\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StackService
{
    protected array $stacks;

    public function __construct()
    {
        $this->stacks = config('ai-text-editor.stacks', []);
    }

    public function getAvailableStacks(): array
    {
        return array_keys($this->stacks);
    }

    public function getStackInfo(string $stack): array
    {
        return $this->stacks[$stack] ?? [];
    }

    public function isValidStack(string $stack): bool
    {
        return array_key_exists($stack, $this->stacks);
    }

    public function getComposerDependencies(string $stack): array
    {
        return $this->stacks[$stack]['dependencies']['composer'] ?? [];
    }

    public function getNpmDependencies(string $stack, bool $asAssoc = false): array
    {
        $deps = $this->stacks[$stack]['dependencies']['npm'] ?? [];
        
        if ($asAssoc) {
            $assoc = [];
            foreach ($deps as $dep) {
                if (str_contains($dep, '@')) {
                    [$name, $version] = explode('@', $dep, 2);
                    $assoc[$name] = $version;
                } else {
                    $assoc[$dep] = 'latest';
                }
            }
            return $assoc;
        }
        
        return $deps;
    }

    public function getPackageScripts(string $stack): array
    {
        $scripts = [
            'dev' => 'vite',
            'build' => 'vite build',
            'preview' => 'vite preview'
        ];

        switch ($stack) {
            case 'vue-spa':
                $scripts = array_merge($scripts, [
                    'serve' => 'vite --host',
                    'type-check' => 'vue-tsc --noEmit'
                ]);
                break;
            case 'react-nextjs':
                $scripts = array_merge($scripts, [
                    'start' => 'next start',
                    'lint' => 'next lint',
                    'type-check' => 'tsc --noEmit'
                ]);
                break;
        }

        return $scripts;
    }

    public function getViteConfig(string $stack): string
    {
        $baseConfig = "import { defineConfig } from 'vite';\n";
        $baseConfig .= "import laravel from 'laravel-vite-plugin';\n\n";

        switch ($stack) {
            case 'blade-livewire':
                return $baseConfig . $this->getBladeViteConfig();
            case 'vue-spa':
                return $baseConfig . $this->getVueViteConfig();
            case 'react-nextjs':
                return $this->getNextJsConfig();
            default:
                return $baseConfig . $this->getBladeViteConfig();
        }
    }

    protected function getBladeViteConfig(): string
    {
        return "export default defineConfig({\n" .
            "    plugins: [\n" .
            "        laravel({\n" .
            "            input: ['resources/css/app.css', 'resources/js/app.js'],\n" .
            "            refresh: true,\n" .
            "        }),\n" .
            "    ],\n" .
            "});\n";
    }

    protected function getVueViteConfig(): string
    {
        return "import vue from '@vitejs/plugin-vue';\n\n" .
            "export default defineConfig({\n" .
            "    plugins: [\n" .
            "        laravel({\n" .
            "            input: ['resources/css/app.css', 'resources/js/app.js'],\n" .
            "            refresh: true,\n" .
            "        }),\n" .
            "        vue({\n" .
            "            template: {\n" .
            "                transformAssetUrls: {\n" .
            "                    base: null,\n" .
            "                    includeAbsolute: false,\n" .
            "                },\n" .
            "            },\n" .
            "        }),\n" .
            "    ],\n" .
            "    resolve: {\n" .
            "        alias: {\n" .
            "            vue: 'vue/dist/vue.esm-bundler.js',\n" .
            "        },\n" .
            "    },\n" .
            "});\n";
    }

    protected function getNextJsConfig(): string
    {
        return "const { defineConfig } = require('next');\n\n" .
            "module.exports = defineConfig({\n" .
            "    reactStrictMode: true,\n" .
            "    swcMinify: true,\n" .
            "    env: {\n" .
            "        NEXT_PUBLIC_API_URL: process.env.APP_URL + '/api',\n" .
            "    },\n" .
            "    async rewrites() {\n" .
            "        return [\n" .
            "            {\n" .
            "                source: '/api/:path*',\n" .
            "                destination: process.env.APP_URL + '/api/:path*',\n" .
            "            },\n" .
            "        ];\n" .
            "    },\n" .
            "});\n";
    }

    public function getTailwindConfig(): string
    {
        return "/** @type {import('tailwindcss').Config} */\n" .
            "export default {\n" .
            "    content: [\n" .
            "        './resources/**/*.blade.php',\n" .
            "        './resources/**/*.js',\n" .
            "        './resources/**/*.vue',\n" .
            "        './resources/**/*.tsx',\n" .
            "        './resources/**/*.ts',\n" .
            "        './app/**/*.php',\n" .
            "        './resources/views/**/*.blade.php',\n" .
            "    ],\n" .
            "    theme: {\n" .
            "        extend: {\n" .
            "            colors: {\n" .
            "                primary: {\n" .
            "                    50: '#eff6ff',\n" .
            "                    500: '#3b82f6',\n" .
            "                    600: '#2563eb',\n" .
            "                    700: '#1d4ed8',\n" .
            "                },\n" .
            "            },\n" .
            "        },\n" .
            "    },\n" .
            "    plugins: [\n" .
            "        require('@tailwindcss/forms'),\n" .
            "        require('@tailwindcss/typography'),\n" .
            "    ],\n" .
            "};\n";
    }

    public function copyStackFiles(string $stack): void
    {
        $stubPath = __DIR__ . "/../../stubs/stacks/{$stack}";
        
        if (!File::exists($stubPath)) {
            return;
        }

        // Copy JavaScript files
        $this->copyJsFiles($stack);
        
        // Copy view files
        $this->copyViewFiles($stack);
        
        // Copy component files
        $this->copyComponentFiles($stack);
    }

    protected function copyJsFiles(string $stack): void
    {
        $jsPath = resource_path('js');
        
        if (!File::exists($jsPath)) {
            File::makeDirectory($jsPath, 0755, true);
        }

        $stubJsPath = __DIR__ . "/../../stubs/stacks/{$stack}/js";
        
        if (File::exists($stubJsPath)) {
            File::copyDirectory($stubJsPath, $jsPath);
        }
    }

    protected function copyViewFiles(string $stack): void
    {
        $viewsPath = resource_path('views');
        
        if (!File::exists($viewsPath)) {
            File::makeDirectory($viewsPath, 0755, true);
        }

        $stubViewsPath = __DIR__ . "/../../stubs/stacks/{$stack}/views";
        
        if (File::exists($stubViewsPath)) {
            File::copyDirectory($stubViewsPath, $viewsPath);
        }
    }

    protected function copyComponentFiles(string $stack): void
    {
        $componentsPath = resource_path('views/components');
        
        if (!File::exists($componentsPath)) {
            File::makeDirectory($componentsPath, 0755, true);
        }

        $stubComponentsPath = __DIR__ . "/../../stubs/stacks/{$stack}/components";
        
        if (File::exists($stubComponentsPath)) {
            File::copyDirectory($stubComponentsPath, $componentsPath);
        }
    }

    public function createStarterPages(string $stack): void
    {
        switch ($stack) {
            case 'blade-livewire':
                $this->createBladeStarterPages();
                break;
            case 'vue-spa':
                $this->createVueStarterPages();
                break;
            case 'react-nextjs':
                $this->createReactStarterPages();
                break;
        }
    }

    protected function createBladeStarterPages(): void
    {
        // Create Livewire components
        $this->createLivewireComponent('Dashboard');
        $this->createLivewireComponent('UserProfile');
        $this->createLivewireComponent('Settings');
    }

    protected function createVueStarterPages(): void
    {
        // Create Vue components
        $this->createVueComponent('Dashboard');
        $this->createVueComponent('UserProfile');
        $this->createVueComponent('Settings');
    }

    protected function createReactStarterPages(): void
    {
        // Create React components
        $this->createReactComponent('Dashboard');
        $this->createReactComponent('UserProfile');
        $this->createReactComponent('Settings');
    }

    protected function createLivewireComponent(string $name): void
    {
        $componentPath = app_path("Livewire/{$name}.php");
        $viewPath = resource_path("views/livewire/{$name}.blade.php");
        
        $componentContent = $this->getLivewireComponentStub($name);
        $viewContent = $this->getLivewireViewStub($name);
        
        File::put($componentPath, $componentContent);
        File::put($viewPath, $viewContent);
    }

    protected function createVueComponent(string $name): void
    {
        $componentPath = resource_path("js/components/{$name}.vue");
        
        if (!File::exists(dirname($componentPath))) {
            File::makeDirectory(dirname($componentPath), 0755, true);
        }
        
        $componentContent = $this->getVueComponentStub($name);
        File::put($componentPath, $componentContent);
    }

    protected function createReactComponent(string $name): void
    {
        $componentPath = resource_path("js/components/{$name}.tsx");
        
        if (!File::exists(dirname($componentPath))) {
            File::makeDirectory(dirname($componentPath), 0755, true);
        }
        
        $componentContent = $this->getReactComponentStub($name);
        File::put($componentPath, $componentContent);
    }

    protected function getLivewireComponentStub(string $name): string
    {
        return "<?php\n\nnamespace App\Livewire;\n\nuse Livewire\Component;\n\nclass {$name} extends Component\n{\n    public function render()\n    {\n        return view('livewire.{$name}');\n    }\n}\n";
    }

    protected function getLivewireViewStub(string $name): string
    {
        return "<div>\n    <h1>{$name}</h1>\n    <p>This is the {$name} component.</p>\n</div>\n";
    }

    protected function getVueComponentStub(string $name): string
    {
        return "<template>\n    <div class=\"{$name}\">\n        <h1>{$name}</h1>\n        <p>This is the {$name} component.</p>\n    </div>\n</template>\n\n<script setup>\n// Component logic here\n</script>\n\n<style scoped>\n/* Component styles here */\n</style>\n";
    }

    protected function getReactComponentStub(string $name): string
    {
        return "import React from 'react';\n\nconst {$name}: React.FC = () => {\n    return (\n        <div className=\"{$name}\">\n            <h1>{$name}</h1>\n            <p>This is the {$name} component.</p>\n        </div>\n    );\n};\n\nexport default {$name};\n";
    }

    public function setupRoutes(string $stack): void
    {
        $routesPath = base_path('routes/web.php');
        $routesContent = File::get($routesPath);
        
        $stackRoutes = $this->getStackRoutes($stack);
        
        if (!str_contains($routesContent, $stackRoutes)) {
            File::append($routesPath, "\n" . $stackRoutes);
        }
    }

    protected function getStackRoutes(string $stack): string
    {
        switch ($stack) {
            case 'blade-livewire':
                return $this->getBladeLivewireRoutes();
            case 'vue-spa':
                return $this->getVueSpaRoutes();
            case 'react-nextjs':
                return $this->getReactNextjsRoutes();
            default:
                return '';
        }
    }

    protected function getBladeLivewireRoutes(): string
    {
        return "\n// Multi-Stack Routes\n" .
            "Route::get('/dashboard', App\Livewire\Dashboard::class)->name('dashboard');\n" .
            "Route::get('/profile', App\Livewire\UserProfile::class)->name('profile');\n" .
            "Route::get('/settings', App\Livewire\Settings::class)->name('settings');\n";
    }

    protected function getVueSpaRoutes(): string
    {
        return "\n// API Routes for Vue SPA\n" .
            "Route::prefix('api')->group(function () {\n" .
            "    Route::get('/user', function (Request \$request) {\n" .
            "        return \$request->user();\n" .
            "    });\n" .
            "    Route::get('/dashboard', function () {\n" .
            "        return response()->json(['message' => 'Dashboard data']);\n" .
            "    });\n" .
            "});\n" .
            "\n// SPA Route - must be last\n" .
            "Route::get('/{any}', function () {\n" .
            "    return view('app');\n" .
            "})->where('any', '.*');\n";
    }

    protected function getReactNextjsRoutes(): string
    {
        return "\n// API Routes for React/Next.js\n" .
            "Route::prefix('api')->group(function () {\n" .
            "    Route::get('/user', function (Request \$request) {\n" .
            "        return \$request->user();\n" .
            "    });\n" .
            "    Route::get('/dashboard', function () {\n" .
            "        return response()->json(['message' => 'Dashboard data']);\n" .
            "    });\n" .
            "});\n";
    }
}