<?php

namespace AiEditor\AiTextEditor\Providers;

use AiEditor\AiTextEditor\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiProvider implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('ai-text-editor.ai.providers.openai.api_key');
        $this->model = config('ai-text-editor.ai.providers.openai.model');
        $this->baseUrl = config('ai-text-editor.ai.providers.openai.base_url');
    }

    public function generate(string $prompt, array $options = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $options['max_tokens'] ?? 2000,
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['choices'][0]['message']['content'],
            'model' => $data['model'],
            'usage' => $data['usage'] ?? null,
        ];
    }

    public function edit(string $text, string $instruction, array $options = []): array
    {
        $prompt = "Please edit the following text according to this instruction: {$instruction}\n\nText to edit:\n{$text}";
        
        return $this->generate($prompt, $options);
    }

    public function summarize(string $text, array $options = []): array
    {
        $prompt = "Please provide a concise summary of the following text:\n\n{$text}";
        
        return $this->generate($prompt, $options);
    }

    public function complete(string $text, array $options = []): array
    {
        $prompt = "Please complete the following text in a natural and coherent way:\n\n{$text}";
        
        return $this->generate($prompt, $options);
    }

    public function getName(): string
    {
        return 'openai';
    }

    public function getInfo(): array
    {
        return [
            'name' => 'OpenAI',
            'models' => ['gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo'],
            'capabilities' => ['generate', 'edit', 'summarize', 'complete'],
            'configured' => $this->isConfigured(),
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->model);
    }
}
