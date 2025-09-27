<?php

namespace LaravelDynamicStarterKit\Advanced\Console\Commands;

use Illuminate\Console\Command;
use LaravelDynamicStarterKit\Advanced\Services\AiService;
use LaravelDynamicStarterKit\Advanced\Services\FeatureService;

class AiCommand extends Command
{
    protected $signature = 'starter-kit:ai 
                            {action : The action to perform (generate, providers, test)}
                            {prompt? : The AI prompt}
                            {--provider= : AI provider to use}
                            {--category=custom : Feature category}
                            {--type=text : Content type (text, email, blog, documentation, code)}
                            {--max-tokens=2000 : Maximum tokens}
                            {--temperature=0.7 : Temperature setting}';

    protected $description = 'AI-powered features for Laravel Advanced Dynamic Starter Kit';

    public function handle(AiService $aiService, FeatureService $featureService): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'generate':
                return $this->generateContent($aiService, $featureService);
            case 'providers':
                return $this->listProviders($aiService);
            case 'test':
                return $this->testProvider($aiService);
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    protected function generateContent(AiService $aiService, FeatureService $featureService): int
    {
        $prompt = $this->argument('prompt');
        $provider = $this->option('provider');
        $category = $this->option('category');
        $type = $this->option('type');

        if (!$prompt) {
            $prompt = $this->ask('Enter your AI prompt');
        }

        $this->info("🤖 Generating content with AI...");
        $this->line("Prompt: {$prompt}");
        $this->line("Provider: " . ($provider ?: 'default'));
        $this->line("Type: {$type}");
        $this->newLine();

        $options = [
            'max_tokens' => (int) $this->option('max-tokens'),
            'temperature' => (float) $this->option('temperature'),
        ];

        try {
            if ($type === 'feature') {
                $result = $aiService->generateFeature($prompt, $category, $provider);
                
                if ($result['success']) {
                    $this->info("✅ Feature generated successfully!");
                    $this->newLine();
                    $this->line("Name: {$result['name']}");
                    $this->line("Description: {$result['description']}");
                    $this->line("Type: {$result['type']}");
                    $this->line("Provider: {$result['provider']}");
                    
                    if ($this->confirm('Do you want to create this feature?')) {
                        $feature = $featureService->createFeature(
                            $result['name'],
                            $result['description'],
                            $category,
                            array_merge($result['configuration'], ['ai_generated' => true])
                        );
                        
                        $this->info("✅ Feature '{$feature->name}' created successfully!");
                    }
                } else {
                    $this->error("❌ Feature generation failed: {$result['error']}");
                    return 1;
                }
            } else {
                $result = $aiService->generateContent($prompt, $type, $provider, $options);
                
                if ($result['success']) {
                    $this->info("✅ Content generated successfully!");
                    $this->newLine();
                    $this->line("Provider: {$result['provider']}");
                    $this->line("Model: " . ($result['model'] ?? 'N/A'));
                    $this->newLine();
                    $this->line("Generated Content:");
                    $this->line("────────────────────────────────────────");
                    $this->line($result['content']);
                    $this->line("────────────────────────────────────────");
                    
                    if ($this->confirm('Do you want to save this content to a file?')) {
                        $filename = $this->ask('Enter filename', 'generated_content.txt');
                        File::put($filename, $result['content']);
                        $this->info("✅ Content saved to {$filename}");
                    }
                } else {
                    $this->error("❌ Content generation failed: {$result['error']}");
                    return 1;
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ AI generation failed: {$e->getMessage()}");
            return 1;
        }
    }

    protected function listProviders(AiService $aiService): int
    {
        $this->info('🤖 Available AI Providers');
        $this->newLine();

        $providers = $aiService->getAvailableProviders();

        foreach ($providers as $provider) {
            $info = $aiService->getProviderInfo($provider);
            
            $this->line("📦 {$provider}");
            $this->line("  Name: {$info['name']}");
            $this->line("  Configured: " . ($info['configured'] ? '✅ Yes' : '❌ No'));
            $this->line("  Capabilities: " . implode(', ', $info['capabilities']));
            
            if (!empty($info['models'])) {
                $this->line("  Models: " . implode(', ', $info['models']));
            }
            
            $this->newLine();
        }

        return 0;
    }

    protected function testProvider(AiService $aiService): int
    {
        $provider = $this->option('provider');

        if (!$provider) {
            $providers = $aiService->getAvailableProviders();
            $provider = $this->choice('Select provider to test', $providers);
        }

        $this->info("🧪 Testing AI provider: {$provider}");
        $this->newLine();

        try {
            $testPrompt = "Hello, this is a test message. Please respond with a simple greeting.";
            
            $result = $aiService->generateContent($testPrompt, 'text', $provider, [
                'max_tokens' => 100,
                'temperature' => 0.5,
            ]);

            if ($result['success']) {
                $this->info("✅ Provider test successful!");
                $this->line("Response: {$result['content']}");
                $this->line("Model: " . ($result['model'] ?? 'N/A'));
                $this->line("Provider: {$result['provider']}");
                
                if (isset($result['usage'])) {
                    $this->line("Usage: " . json_encode($result['usage']));
                }
            } else {
                $this->error("❌ Provider test failed: {$result['error']}");
                return 1;
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Provider test failed: {$e->getMessage()}");
            return 1;
        }
    }
}
