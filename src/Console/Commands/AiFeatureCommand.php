<?php

namespace AiEditor\AiTextEditor\Console\Commands;

use Illuminate\Console\Command;
use AiEditor\AiTextEditor\Services\AiFeatureBuilderService;
use AiEditor\AiTextEditor\Models\DynamicFeature;

class AiFeatureCommand extends Command
{
    protected $signature = 'ai-editor:ai-feature 
                            {action : The action to perform (generate, list, toggle, delete, regenerate)}
                            {prompt? : The AI prompt for feature generation}
                            {--category=custom : The feature category}
                            {--provider= : AI provider to use}
                            {--name= : Feature name for other actions}';

    protected $description = 'AI-powered feature generation and management for Laravel Multi-Stack';

    public function handle(AiFeatureBuilderService $aiService): int
    {
        $action = $this->argument('action');
        $prompt = $this->argument('prompt');
        $category = $this->option('category');
        $provider = $this->option('provider');
        $name = $this->option('name');

        switch ($action) {
            case 'generate':
                return $this->generateFeature($aiService, $prompt, $category, $provider);
            case 'list':
                return $this->listFeatures();
            case 'toggle':
                return $this->toggleFeature($name);
            case 'delete':
                return $this->deleteFeature($name);
            case 'regenerate':
                return $this->regenerateFeature($aiService, $name);
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    protected function generateFeature(AiFeatureBuilderService $aiService, ?string $prompt, string $category, ?string $provider): int
    {
        if (!$prompt) {
            $prompt = $this->ask('Enter your AI prompt for feature generation');
        }

        if (!$prompt) {
            $this->error('Prompt is required for feature generation');
            return 1;
        }

        $this->info("🤖 Generating feature with AI...");
        $this->line("Prompt: {$prompt}");
        $this->line("Category: {$category}");
        $this->line("Provider: " . ($provider ?: 'default'));
        $this->newLine();

        try {
            $result = $aiService->generateFeature($prompt, $category, $provider);
            
            if ($result['success']) {
                $feature = $result['feature'];
                $this->info("✅ Feature generated successfully!");
                $this->newLine();
                
                $this->line("Name: {$feature->name}");
                $this->line("Description: {$feature->description}");
                $this->line("Type: {$feature->type}");
                $this->line("Category: {$feature->category}");
                $this->line("Provider: {$result['provider']}");
                $this->line("Model: " . ($result['model'] ?? 'N/A'));
                
                $this->newLine();
                $this->info("📁 Generated Files:");
                $generatedCode = $result['generated_code'];
                
                if ($generatedCode['model']) {
                    $this->line("  ✓ Model: app/Models/{$feature->getModelName()}.php");
                }
                if ($generatedCode['controller']) {
                    $this->line("  ✓ Controller: app/Http/Controllers/{$feature->getControllerName()}.php");
                }
                if ($generatedCode['migration']) {
                    $this->line("  ✓ Migration: database/migrations/create_{$feature->getTableName()}_table.php");
                }
                if (!empty($generatedCode['views'])) {
                    $this->line("  ✓ Views: resources/views/{$feature->getViewPath()}/");
                }
                if ($generatedCode['routes']) {
                    $this->line("  ✓ Routes: routes/web.php");
                }
                if ($generatedCode['api_routes']) {
                    $this->line("  ✓ API Routes: routes/api.php");
                }
                
                $this->newLine();
                $this->info("🚀 Next steps:");
                $this->line("1. Run migrations: php artisan migrate");
                $this->line("2. Visit: /{$feature->slug}");
                $this->line("3. API endpoint: /api/v1/{$feature->slug}");
                
                return 0;
            } else {
                $this->error("❌ Feature generation failed: {$result['error']}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Feature generation failed: {$e->getMessage()}");
            return 1;
        }
    }

    protected function listFeatures(): int
    {
        $this->info('🤖 AI-Generated Features');
        $this->newLine();

        $features = DynamicFeature::with(['versions'])->get();
        $aiFeatures = $features->where('is_ai_generated', true);
        $regularFeatures = $features->where('is_ai_generated', false);

        if ($aiFeatures->count() > 0) {
            $this->info('AI-Generated Features:');
            $this->table(
                ['ID', 'Name', 'Category', 'Type', 'Status', 'Provider', 'Created'],
                $aiFeatures->map(function ($feature) {
                    return [
                        $feature->id,
                        $feature->name,
                        $feature->category,
                        $feature->type,
                        $feature->is_enabled ? '✅ Enabled' : '❌ Disabled',
                        $feature->ai_provider ?? 'N/A',
                        $feature->created_at->format('M d, Y'),
                    ];
                })->toArray()
            );
            $this->newLine();
        }

        if ($regularFeatures->count() > 0) {
            $this->info('Regular Features:');
            $this->table(
                ['ID', 'Name', 'Category', 'Type', 'Status', 'Created'],
                $regularFeatures->map(function ($feature) {
                    return [
                        $feature->id,
                        $feature->name,
                        $feature->category,
                        $feature->type,
                        $feature->is_enabled ? '✅ Enabled' : '❌ Disabled',
                        $feature->created_at->format('M d, Y'),
                    ];
                })->toArray()
            );
        }

        if ($features->count() === 0) {
            $this->info('No features found. Generate your first feature with:');
            $this->line('php artisan multi-stack:ai-feature generate "Create a blog system"');
        }

        return 0;
    }

    protected function toggleFeature(?string $name): int
    {
        if (!$name) {
            $name = $this->ask('Feature name to toggle');
        }

        $feature = DynamicFeature::where('name', $name)->orWhere('slug', $name)->first();

        if (!$feature) {
            $this->error("Feature not found: {$name}");
            return 1;
        }

        $enabled = $feature->toggle();
        $status = $enabled ? 'enabled' : 'disabled';

        $this->info("✅ Feature '{$feature->name}' has been {$status}");

        return 0;
    }

    protected function deleteFeature(?string $name): int
    {
        if (!$name) {
            $name = $this->ask('Feature name to delete');
        }

        $feature = DynamicFeature::where('name', $name)->orWhere('slug', $name)->first();

        if (!$feature) {
            $this->error("Feature not found: {$name}");
            return 1;
        }

        if (!$this->confirm("Are you sure you want to delete feature '{$feature->name}'? This action cannot be undone.")) {
            $this->info('Deletion cancelled.');
            return 0;
        }

        try {
            // Delete associated files
            $this->deleteFeatureFiles($feature);
            
            // Delete feature
            $feature->delete();
            
            $this->info("✅ Feature '{$feature->name}' has been deleted");

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to delete feature: {$e->getMessage()}");
            return 1;
        }
    }

    protected function regenerateFeature(AiFeatureBuilderService $aiService, ?string $name): int
    {
        if (!$name) {
            $name = $this->ask('Feature name to regenerate');
        }

        $feature = DynamicFeature::where('name', $name)->orWhere('slug', $name)->first();

        if (!$feature) {
            $this->error("Feature not found: {$name}");
            return 1;
        }

        if (!$this->confirm("Are you sure you want to regenerate feature '{$feature->name}'?")) {
            $this->info('Regeneration cancelled.');
            return 0;
        }

        try {
            $this->info("🤖 Regenerating feature with AI...");
            
            // Use original prompt if available
            $prompt = $feature->ai_prompt ?? "Regenerate the {$feature->name} feature";
            $provider = $feature->ai_provider;
            
            $result = $aiService->generateFeature($prompt, $feature->category, $provider);
            
            if ($result['success']) {
                $this->info("✅ Feature '{$feature->name}' has been regenerated");
                
                // Show what was updated
                $this->newLine();
                $this->info("📁 Updated Files:");
                $generatedCode = $result['generated_code'];
                
                if ($generatedCode['model']) {
                    $this->line("  ✓ Model: app/Models/{$feature->getModelName()}.php");
                }
                if ($generatedCode['controller']) {
                    $this->line("  ✓ Controller: app/Http/Controllers/{$feature->getControllerName()}.php");
                }
                if ($generatedCode['migration']) {
                    $this->line("  ✓ Migration: database/migrations/create_{$feature->getTableName()}_table.php");
                }
                if (!empty($generatedCode['views'])) {
                    $this->line("  ✓ Views: resources/views/{$feature->getViewPath()}/");
                }
                
                return 0;
            } else {
                $this->error("❌ Feature regeneration failed: {$result['error']}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Failed to regenerate feature: {$e->getMessage()}");
            return 1;
        }
    }

    protected function deleteFeatureFiles(DynamicFeature $feature): void
    {
        // Delete model
        $modelPath = app_path("Models/{$feature->getModelName()}.php");
        if (\Illuminate\Support\Facades\File::exists($modelPath)) {
            \Illuminate\Support\Facades\File::delete($modelPath);
        }

        // Delete controller
        $controllerPath = app_path("Http/Controllers/{$feature->getControllerName()}.php");
        if (\Illuminate\Support\Facades\File::exists($controllerPath)) {
            \Illuminate\Support\Facades\File::delete($controllerPath);
        }

        // Delete views
        $viewPath = resource_path("views/{$feature->getViewPath()}");
        if (\Illuminate\Support\Facades\File::exists($viewPath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($viewPath);
        }

        $this->warn("Note: Routes need to be manually removed from route files");
    }
}
