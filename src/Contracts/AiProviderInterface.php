<?php

namespace AiEditor\AiTextEditor\Contracts;

interface AiProviderInterface
{
    public function generate(string $prompt, array $options = []): array;
    public function edit(string $text, string $instruction, array $options = []): array;
    public function summarize(string $text, array $options = []): array;
    public function complete(string $text, array $options = []): array;
    public function getName(): string;
    public function getInfo(): array;
    public function isConfigured(): bool;
}
