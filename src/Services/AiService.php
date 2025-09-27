<?php

namespace LaravelDynamicStarterKit\Advanced\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LaravelDynamicStarterKit\Advanced\Contracts\AiProviderInterface;
use LaravelDynamicStarterKit\Advanced\Providers\OpenAiProvider;
use LaravelDynamicStarterKit\Advanced\Providers\AnthropicProvider;
use LaravelDynamicStarterKit\Advanced\Providers\GoogleProvider;
use LaravelDynamicStarterKit\Advanced\Providers\HuggingFaceProvider;
use LaravelDynamicStarterKit\Advanced\Providers\OpenRouterProvider;

class AiService
{
    protected array $providers = [];
    protected string $defaultProvider;

    public function __construct()
    {
        $this->defaultProvider = config('advanced-starter-kit.ai.default_provider', 'openai');
        $this->initializeProviders();
    }

    protected function initializeProviders(): void
    {
        $this->providers = [
            'openai' => new OpenAiProvider(),
            'anthropic' => new AnthropicProvider(),
            'google' => new GoogleProvider(),
            'huggingface' => new HuggingFaceProvider(),
            'openrouter' => new OpenRouterProvider(),
        ];
    }

    public function generateFeature(string $prompt, string $category = 'custom', ?string $provider = null): array
    {
        $provider = $this->getProvider($provider);
        
        $systemPrompt = $this->getFeatureGenerationPrompt($category);
        $fullPrompt = $systemPrompt . "\n\nUser Request: " . $prompt;
        
        try {
            $response = $provider->generate($fullPrompt, [
                'max_tokens' => 4000,
                'temperature' => 0.7,
            ]);
            
            $featureData = $this->parseFeatureResponse($response['content']);
            
            return [
                'success' => true,
                'name' => $featureData['name'],
                'description' => $featureData['description'],
                'type' => $featureData['type'],
                'configuration' => $featureData['configuration'],
                'provider' => $provider->getName(),
                'model' => $response['model'] ?? null,
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('AI Feature Generation failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
                'prompt' => $prompt,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider->getName(),
            ];
        }
    }

    public function updateFeature(string $featureName, string $updatePrompt, ?string $provider = null): array
    {
        $provider = $this->getProvider($provider);
        
        $systemPrompt = $this->getFeatureUpdatePrompt();
        $fullPrompt = $systemPrompt . "\n\nFeature: " . $featureName . "\nUpdate Request: " . $updatePrompt;
        
        try {
            $response = $provider->generate($fullPrompt, [
                'max_tokens' => 3000,
                'temperature' => 0.7,
            ]);
            
            $updateData = $this->parseFeatureUpdateResponse($response['content']);
            
            return [
                'success' => true,
                'updates' => $updateData,
                'provider' => $provider->getName(),
                'model' => $response['model'] ?? null,
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('AI Feature Update failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
                'feature' => $featureName,
                'prompt' => $updatePrompt,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider->getName(),
            ];
        }
    }

    public function generateContent(string $prompt, string $type = 'text', ?string $provider = null, array $options = []): array
    {
        $provider = $this->getProvider($provider);
        
        $systemPrompt = $this->getContentGenerationPrompt($type);
        $fullPrompt = $systemPrompt . "\n\nUser Request: " . $prompt;
        
        try {
            $response = $provider->generate($fullPrompt, array_merge([
                'max_tokens' => $options['max_tokens'] ?? 2000,
                'temperature' => $options['temperature'] ?? 0.7,
            ], $options));
            
            return [
                'success' => true,
                'content' => $response['content'],
                'type' => $type,
                'provider' => $provider->getName(),
                'model' => $response['model'] ?? null,
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('AI Content Generation failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
                'prompt' => $prompt,
                'type' => $type,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider->getName(),
            ];
        }
    }

    protected function getFeatureGenerationPrompt(string $category): string
    {
        return "You are an expert Laravel developer and feature generator. Generate a complete Laravel feature based on the user's request.

Category: {$category}

Please respond with a JSON object containing:
{
    \"name\": \"Feature Name\",
    \"description\": \"Brief description of the feature\",
    \"type\": \"crud|api|dashboard|component|integration|custom\",
    \"configuration\": {
        \"model\": \"ModelName\",
        \"table\": \"table_name\",
        \"fields\": [
            {\"name\": \"field_name\", \"type\": \"string|integer|text|boolean|json\", \"nullable\": true/false, \"default\": \"default_value\"}
        ],
        \"permissions\": [\"permission1\", \"permission2\"],
        \"middleware\": [\"auth\", \"role:admin\"],
        \"api_enabled\": true/false,
        \"dashboard_widget\": true/false
    }
}

Make sure the feature is production-ready and follows Laravel best practices.";
    }

    protected function getFeatureUpdatePrompt(): string
    {
        return "You are an expert Laravel developer. Update the existing feature based on the user's request.

Please respond with a JSON object containing:
{
    \"updates\": {
        \"description\": \"Updated description\",
        \"configuration\": {
            \"fields\": [/* updated fields */],
            \"permissions\": [/* updated permissions */],
            \"middleware\": [/* updated middleware */]
        },
        \"code_changes\": {
            \"model\": \"/* changes to model */\",
            \"controller\": \"/* changes to controller */\",
            \"views\": \"/* changes to views */\"
        }
    }
}

Provide specific, actionable updates that can be implemented.";
    }

    protected function getContentGenerationPrompt(string $type): string
    {
        $prompts = [
            'text' => 'Generate high-quality, engaging text content.',
            'email' => 'Generate professional email content.',
            'blog' => 'Generate blog post content with proper structure.',
            'documentation' => 'Generate technical documentation.',
            'api_docs' => 'Generate API documentation.',
            'code' => 'Generate clean, well-documented code.',
            'html' => 'Generate semantic HTML markup.',
            'css' => 'Generate modern CSS styles.',
            'javascript' => 'Generate clean JavaScript code.',
        ];

        return $prompts[$type] ?? 'Generate high-quality content.';
    }

    protected function parseFeatureResponse(string $content): array
    {
        // Extract JSON from the response
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
            $data = json_decode($jsonString, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }
        
        // Fallback parsing if JSON extraction fails
        return $this->fallbackParseFeatureResponse($content);
    }

    protected function parseFeatureUpdateResponse(string $content): array
    {
        // Extract JSON from the response
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
            $data = json_decode($jsonString, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }
        
        // Fallback parsing
        return [
            'updates' => [
                'description' => 'Feature updated',
                'configuration' => [],
                'code_changes' => []
            ]
        ];
    }

    protected function fallbackParseFeatureResponse(string $content): array
    {
        // Extract name
        preg_match('/name[:\s]+([^\n,]+)/i', $content, $nameMatches);
        $name = $nameMatches[1] ?? 'Generated Feature';
        
        // Extract description
        preg_match('/description[:\s]+([^\n,]+)/i', $content, $descMatches);
        $description = $descMatches[1] ?? 'AI Generated Feature';
        
        // Extract type
        preg_match('/type[:\s]+([^\n,]+)/i', $content, $typeMatches);
        $type = $typeMatches[1] ?? 'custom';
        
        return [
            'name' => trim($name),
            'description' => trim($description),
            'type' => trim($type),
            'configuration' => [
                'model' => 'GeneratedModel',
                'table' => 'generated_table',
                'fields' => [
                    ['name' => 'name', 'type' => 'string', 'nullable' => false],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                ],
                'permissions' => ['view', 'create', 'edit', 'delete'],
                'middleware' => ['auth'],
                'api_enabled' => true,
                'dashboard_widget' => false,
            ]
        ];
    }

    protected function getProvider(?string $provider = null): AiProviderInterface
    {
        $provider = $provider ?? $this->defaultProvider;
        
        if (!isset($this->providers[$provider])) {
            throw new \InvalidArgumentException("AI provider '{$provider}' not supported");
        }

        return $this->providers[$provider];
    }

    public function getAvailableProviders(): array
    {
        return array_keys($this->providers);
    }

    public function getProviderInfo(string $provider): array
    {
        if (!isset($this->providers[$provider])) {
            throw new \InvalidArgumentException("AI provider '{$provider}' not supported");
        }

        return $this->providers[$provider]->getInfo();
    }

    public function generateTextEditorContent(string $prompt, ?string $provider = null, array $options = []): array
    {
        $provider = $this->getProvider($provider);
        
        try {
            $response = $provider->generate($prompt, array_merge([
                'max_tokens' => $options['max_tokens'] ?? 2000,
                'temperature' => $options['temperature'] ?? 0.7,
            ], $options));
            
            return [
                'success' => true,
                'content' => $response['content'],
                'provider' => $provider->getName(),
                'model' => $response['model'] ?? null,
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('AI Text Editor Generation failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
                'prompt' => $prompt,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider->getName(),
            ];
        }
    }

    public function editTextEditorContent(string $text, string $instruction, ?string $provider = null, array $options = []): array
    {
        $provider = $this->getProvider($provider);
        
        try {
            $prompt = "Please edit the following text according to this instruction: {$instruction}\n\nText to edit:\n{$text}";
            
            $response = $provider->generate($prompt, array_merge([
                'max_tokens' => $options['max_tokens'] ?? 2000,
                'temperature' => $options['temperature'] ?? 0.7,
            ], $options));
            
            return [
                'success' => true,
                'content' => $response['content'],
                'provider' => $provider->getName(),
                'model' => $response['model'] ?? null,
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('AI Text Editor Edit failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
                'text' => $text,
                'instruction' => $instruction,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider->getName(),
            ];
        }
    }

    public function summarizeTextEditorContent(string $text, ?string $provider = null, array $options = []): array
    {
        $provider = $this->getProvider($provider);
        
        try {
            $prompt = "Please provide a concise summary of the following text:\n\n{$text}";
            
            $response = $provider->generate($prompt, array_merge([
                'max_tokens' => $options['max_tokens'] ?? 500,
                'temperature' => $options['temperature'] ?? 0.5,
            ], $options));
            
            return [
                'success' => true,
                'content' => $response['content'],
                'provider' => $provider->getName(),
                'model' => $response['model'] ?? null,
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('AI Text Editor Summarize failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
                'text' => $text,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider->getName(),
            ];
        }
    }

    public function completeTextEditorContent(string $text, ?string $provider = null, array $options = []): array
    {
        $provider = $this->getProvider($provider);
        
        try {
            $prompt = "Please complete the following text in a natural and coherent way:\n\n{$text}";
            
            $response = $provider->generate($prompt, array_merge([
                'max_tokens' => $options['max_tokens'] ?? 1000,
                'temperature' => $options['temperature'] ?? 0.7,
            ], $options));
            
            return [
                'success' => true,
                'content' => $response['content'],
                'provider' => $provider->getName(),
                'model' => $response['model'] ?? null,
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('AI Text Editor Complete failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
                'text' => $text,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider->getName(),
            ];
        }
    }
}