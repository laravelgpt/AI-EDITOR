<?php

namespace AiEditor\AiTextEditor\Providers;

use AiEditor\AiTextEditor\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;

class GoogleProvider implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('ai-text-editor.ai.providers.google.api_key');
        $this->model = config('ai-text-editor.ai.providers.google.model');
    }

    public function generate(string $prompt, array $options = []): array
    {
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => $options['max_tokens'] ?? 2000,
                'temperature' => $options['temperature'] ?? 0.7,
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Google AI API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['candidates'][0]['content']['parts'][0]['text'],
            'model' => $this->model,
            'usage' => $data['usageMetadata'] ?? null,
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
        return 'google';
    }

    public function getInfo(): array
    {
        return [
            'name' => 'Google Gemini',
            'models' => ['gemini-pro', 'gemini-pro-vision'],
            'capabilities' => ['generate', 'edit', 'summarize', 'complete'],
            'configured' => $this->isConfigured(),
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->model);
    }
}
