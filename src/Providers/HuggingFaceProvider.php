<?php

namespace AiEditor\AiTextEditor\Providers;

use AiEditor\AiTextEditor\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;

class HuggingFaceProvider implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('ai-text-editor.ai.providers.huggingface.api_key');
        $this->model = config('ai-text-editor.ai.providers.huggingface.model');
        $this->baseUrl = config('ai-text-editor.ai.providers.huggingface.base_url');
    }

    public function generate(string $prompt, array $options = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/models/' . $this->model, [
            'inputs' => $prompt,
            'parameters' => [
                'max_length' => $options['max_tokens'] ?? 200,
                'temperature' => $options['temperature'] ?? 0.7,
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('HuggingFace API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => is_array($data) ? $data[0]['generated_text'] : $data['generated_text'],
            'model' => $this->model,
            'usage' => null,
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
        return 'huggingface';
    }

    public function getInfo(): array
    {
        return [
            'name' => 'HuggingFace',
            'models' => ['microsoft/DialoGPT-medium', 'gpt2', 'facebook/blenderbot-400M-distill'],
            'capabilities' => ['generate', 'edit', 'summarize', 'complete'],
            'configured' => $this->isConfigured(),
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->model);
    }
}
