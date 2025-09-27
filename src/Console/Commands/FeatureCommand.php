<?php

namespace LaravelDynamicStarterKit\Advanced\Console\Commands;

use Illuminate\Console\Command;
use LaravelDynamicStarterKit\Advanced\Services\FeatureService;
use LaravelDynamicStarterKit\Advanced\Services\AiService;
use LaravelDynamicStarterKit\Advanced\Models\Feature;

class FeatureCommand extends Command
{
    protected $signature = 'starter-kit:feature 
                            {action : The action to perform (list, create, toggle, regenerate, delete)}
                            {name? : The feature name}
                            {--category=custom : The feature category}
                            {--description= : The feature description}
                            {--ai : Generate feature using AI}
                            {--prompt= : AI prompt for feature generation}
                            {--provider= : AI provider to use}
                            {--enabled : Enable the feature}
                            {--disabled : Disable the feature}';

    protected $description = 'Manage Laravel Advanced Dynamic Starter Kit Features';

    public function handle(FeatureService $featureService, AiService $aiService): int
    {
        $action = $this->argument('action');
        $name = $this->argument('name');

        switch ($action) {
            case 'list':
                return $this->listFeatures($featureService);
            case 'create':
                return $this->createFeature($featureService, $aiService);
            case 'toggle':
                return $this->toggleFeature($featureService);
            case 'regenerate':
                return $this->regenerateFeature($featureService);
            case 'delete':
                return $this->deleteFeature($featureService);
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    protected function listFeatures(FeatureService $featureService): int
    {
        $this->info('📦 Laravel Advanced Dynamic Starter Kit Features');
        $this->newLine();

        $features = Feature::with(['creator', 'versions'])->get();
        $stats = $featureService->getFeatureStats();

        // Show stats
        $this->info('📊 Feature Statistics:');
        $this->line("Total Features: {$stats['total_features']}");
        $this->line("Enabled Features: {$stats['enabled_features']}");
        $this->line("AI Generated Features: {$stats['ai_generated_features']}");
        $this->newLine();

        // Show features table
        $headers = ['ID', 'Name', 'Category', 'Status', 'AI Generated', 'Created'];
        $rows = [];

        foreach ($features as $feature) {
            $rows[] = [
                $feature->id,
                $feature->name,
                $feature->category_name,
                $feature->is_enabled ? '✅ Enabled' : '❌ Disabled',
                $feature->is_ai_generated ? '🤖 Yes' : '👤 No',
                $feature->created_at->format('M d, Y'),
            ];
        }

        $this->table($headers, $rows);

        // Show features by category
        if (!empty($stats['features_by_category'])) {
            $this->newLine();
            $this->info('Features by Category:');
            foreach ($stats['features_by_category'] as $category => $count) {
                $this->line("  {$category}: {$count}");
            }
        }

        return 0;
    }

    protected function createFeature(FeatureService $featureService, AiService $aiService): int
    {
        $name = $this->argument('name');
        $category = $this->option('category');
        $description = $this->option('description');
        $useAi = $this->option('ai');
        $prompt = $this->option('prompt');
        $provider = $this->option('provider');

        if (!$name) {
            $name = $this->ask('Feature name');
        }

        if (!$description) {
            $description = $this->ask('Feature description');
        }

        if ($useAi && !$prompt) {
            $prompt = $this->ask('AI prompt for feature generation');
        }

        $this->info("Creating feature: {$name}");

        try {
            if ($useAi && $prompt) {
                $feature = $featureService->generateFeatureFromAi($prompt, $category);
                $this->info("✅ AI-generated feature created: {$feature->name}");
            } else {
                $feature = $featureService->createFeature($name, $description, $category);
                $this->info("✅ Feature created: {$feature->name}");
            }

            // Show feature details
            $this->showFeatureDetails($feature);

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create feature: {$e->getMessage()}");
            return 1;
        }
    }

    protected function toggleFeature(FeatureService $featureService): int
    {
        $name = $this->argument('name');

        if (!$name) {
            $name = $this->ask('Feature name to toggle');
        }

        $feature = Feature::where('name', $name)->orWhere('slug', $name)->first();

        if (!$feature) {
            $this->error("Feature not found: {$name}");
            return 1;
        }

        $enabled = $featureService->toggleFeature($feature);
        $status = $enabled ? 'enabled' : 'disabled';

        $this->info("✅ Feature '{$feature->name}' has been {$status}");

        return 0;
    }

    protected function regenerateFeature(FeatureService $featureService): int
    {
        $name = $this->argument('name');

        if (!$name) {
            $name = $this->ask('Feature name to regenerate');
        }

        $feature = Feature::where('name', $name)->orWhere('slug', $name)->first();

        if (!$feature) {
            $this->error("Feature not found: {$name}");
            return 1;
        }

        if (!$this->confirm("Are you sure you want to regenerate feature '{$feature->name}'?")) {
            $this->info('Regeneration cancelled.');
            return 0;
        }

        try {
            $featureService->regenerateFeature($feature);
            $this->info("✅ Feature '{$feature->name}' has been regenerated");

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to regenerate feature: {$e->getMessage()}");
            return 1;
        }
    }

    protected function deleteFeature(FeatureService $featureService): int
    {
        $name = $this->argument('name');

        if (!$name) {
            $name = $this->ask('Feature name to delete');
        }

        $feature = Feature::where('name', $name)->orWhere('slug', $name)->first();

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

    protected function showFeatureDetails(Feature $feature): void
    {
        $this->newLine();
        $this->info("📋 Feature Details:");
        $this->line("Name: {$feature->name}");
        $this->line("Slug: {$feature->slug}");
        $this->line("Description: {$feature->description}");
        $this->line("Category: {$feature->category_name}");
        $this->line("Status: " . ($feature->is_enabled ? '✅ Enabled' : '❌ Disabled'));
        $this->line("AI Generated: " . ($feature->is_ai_generated ? '🤖 Yes' : '👤 No'));
        
        if ($feature->is_ai_generated) {
            $this->line("AI Provider: {$feature->ai_provider}");
            $this->line("AI Prompt: {$feature->ai_prompt}");
        }
        
        $this->line("Created: {$feature->created_at->format('M d, Y H:i')}");
        
        // Show configuration
        if (!empty($feature->configuration)) {
            $this->newLine();
            $this->info("⚙️ Configuration:");
            foreach ($feature->configuration as $key => $value) {
                if (is_array($value)) {
                    $this->line("  {$key}: " . json_encode($value));
                } else {
                    $this->line("  {$key}: {$value}");
                }
            }
        }
    }

    protected function deleteFeatureFiles(Feature $feature): void
    {
        // Delete model
        $modelPath = app_path("Models/{$feature->getModelName()}.php");
        if (File::exists($modelPath)) {
            File::delete($modelPath);
        }

        // Delete controller
        $controllerPath = app_path("Http/Controllers/{$feature->getControllerName()}.php");
        if (File::exists($controllerPath)) {
            File::delete($controllerPath);
        }

        // Delete views
        $viewPath = resource_path("views/{$feature->getViewPath()}");
        if (File::exists($viewPath)) {
            File::deleteDirectory($viewPath);
        }

        // Remove routes (this would require more sophisticated route management)
        $this->warn("Note: Routes need to be manually removed from route files");
    }
}
