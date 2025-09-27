<?php

namespace AiEditor\AiTextEditor\Providers;

use AiEditor\AiTextEditor\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;

class AnthropicProvider implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('ai-text-editor.ai.providers.anthropic.api_key');
        $this->model = config('ai-text-editor.ai.providers.anthropic.model');
    }

    public function generate(string $prompt, array $options = []): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => $options['max_tokens'] ?? 2000,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Anthropic API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['content'][0]['text'],
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
        return 'anthropic';
    }

    public function getInfo(): array
    {
        return [
            'name' => 'Anthropic Claude',
            'models' => ['claude-3-opus-20240229', 'claude-3-sonnet-20240229', 'claude-3-haiku-20240307'],
            'capabilities' => ['generate', 'edit', 'summarize', 'complete'],
            'configured' => $this->isConfigured(),
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->model);
    }
}
