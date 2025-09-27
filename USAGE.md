# AI Text Editor - Usage Guide

This guide provides detailed examples and use cases for the AI-Powered Text Editor package.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Basic Usage](#basic-usage)
3. [Advanced Features](#advanced-features)
4. [API Examples](#api-examples)
5. [Customization](#customization)
6. [Integration Examples](#integration-examples)
7. [Best Practices](#best-practices)

## Quick Start

### 1. Install the Package

```bash
composer require ai-editor/ai-text-editor
```

### 2. Publish and Configure

```bash
# Publish configuration
php artisan vendor:publish --provider="AiEditor\AiTextEditor\AiTextEditorServiceProvider" --tag="config"

# Publish assets
php artisan vendor:publish --provider="AiEditor\AiTextEditor\AiTextEditorServiceProvider" --tag="assets"

# Run migrations
php artisan migrate
```

### 3. Add Environment Variables

```env
OPENAI_API_KEY=your_openai_api_key
ANTHROPIC_API_KEY=your_anthropic_api_key
AI_EDITOR_DEFAULT_PROVIDER=openai
```

### 4. Use in Your Blade Template

```blade
@livewire('ai-text-editor', [
    'content' => 'Start writing here...',
    'theme' => 'auto'
])
```

## Basic Usage

### Simple Text Editor

```blade
<!-- resources/views/editor.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My AI Editor</h1>
    
    @livewire('ai-text-editor', [
        'content' => $initialContent ?? '',
        'theme' => 'light'
    ])
</div>
@endsection
```

### With Custom Configuration

```blade
@livewire('ai-text-editor', [
    'content' => $content,
    'theme' => 'dark',
    'height' => '600px',
    'showMemoryPanel' => true
])
```

## Advanced Features

### Custom AI Prompts

```javascript
// Custom AI generation
const editor = document.querySelector('#ai-editor');
const customPrompt = 'Write a professional email about project updates';

// Use the AI modal
editor.dispatchEvent(new CustomEvent('openAiModal', {
    detail: { action: 'generate', prompt: customPrompt }
}));
```

### Memory System Integration

```php
// In your controller
use AiEditor\AiTextEditor\Services\MemoryService;

class ContentController extends Controller
{
    public function saveVersion(Request $request, MemoryService $memoryService)
    {
        $memory = $memoryService->store(
            $request->content,
            'manual_save',
            'important_document',
            [
                'user_id' => auth()->id(),
                'document_id' => $request->document_id,
                'tags' => ['draft', 'important']
            ]
        );
        
        return response()->json(['success' => true, 'memory_id' => $memory->id]);
    }
    
    public function getVersions(MemoryService $memoryService)
    {
        $versions = $memoryService->getVersions(20, 'important_document');
        return response()->json($versions);
    }
}
```

### Custom AI Provider

```php
<?php

namespace App\Providers;

use AiEditor\AiTextEditor\Contracts\AiProviderInterface;

class CustomAiProvider implements AiProviderInterface
{
    public function generate(string $prompt, array $options = []): array
    {
        // Your custom AI implementation
        $response = $this->callCustomApi($prompt, $options);
        
        return [
            'content' => $response['generated_text'],
            'model' => 'custom-model',
            'usage' => $response['usage'] ?? null,
        ];
    }
    
    public function edit(string $text, string $instruction, array $options = []): array
    {
        $prompt = "Edit the following text: {$instruction}\n\nText: {$text}";
        return $this->generate($prompt, $options);
    }
    
    public function summarize(string $text, array $options = []): array
    {
        $prompt = "Summarize: {$text}";
        return $this->generate($prompt, $options);
    }
    
    public function complete(string $text, array $options = []): array
    {
        $prompt = "Complete: {$text}";
        return $this->generate($prompt, $options);
    }
    
    public function getName(): string
    {
        return 'custom';
    }
    
    public function getInfo(): array
    {
        return [
            'name' => 'Custom AI',
            'models' => ['custom-model-1', 'custom-model-2'],
            'capabilities' => ['generate', 'edit', 'summarize', 'complete'],
            'configured' => true,
        ];
    }
    
    public function isConfigured(): bool
    {
        return true;
    }
    
    private function callCustomApi(string $prompt, array $options): array
    {
        // Your API call implementation
        return [
            'generated_text' => 'Generated content here',
            'usage' => ['tokens' => 100]
        ];
    }
}
```

## API Examples

### Generate Content

```javascript
// Using fetch API
const generateContent = async (prompt, provider = 'openai') => {
    const response = await fetch('/ai-editor/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            prompt: prompt,
            provider: provider,
            options: {
                max_tokens: 1000,
                temperature: 0.7
            }
        })
    });
    
    return await response.json();
};

// Usage
const result = await generateContent('Write a blog post about Laravel');
console.log(result.content);
```

### Edit Content

```javascript
const editContent = async (text, instruction, provider = 'anthropic') => {
    const response = await fetch('/ai-editor/edit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            text: text,
            instruction: instruction,
            provider: provider
        })
    });
    
    return await response.json();
};
```

### Memory Operations

```javascript
// Get memory versions
const getMemories = async (limit = 20, tag = null) => {
    const url = new URL('/ai-editor/memory', window.location.origin);
    if (limit) url.searchParams.set('limit', limit);
    if (tag) url.searchParams.set('tag', tag);
    
    const response = await fetch(url);
    return await response.json();
};

// Restore from memory
const restoreMemory = async (id) => {
    const response = await fetch(`/ai-editor/memory/${id}/restore`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    
    return await response.json();
};

// Search memories
const searchMemories = async (query, tag = null) => {
    const response = await fetch('/ai-editor/memory/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            query: query,
            tag: tag
        })
    });
    
    return await response.json();
};
```

## Integration Examples

### Blog Post Editor

```php
// BlogController.php
class BlogController extends Controller
{
    public function create()
    {
        return view('blog.create');
    }
    
    public function store(Request $request, AiService $aiService, MemoryService $memoryService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'ai_generated' => 'boolean'
        ]);
        
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
            'ai_generated' => $request->ai_generated ?? false
        ]);
        
        // Store in memory if AI generated
        if ($request->ai_generated) {
            $memoryService->store(
                $request->content,
                'blog_post',
                'published',
                [
                    'post_id' => $post->id,
                    'title' => $request->title
                ]
            );
        }
        
        return redirect()->route('blog.show', $post);
    }
}
```

```blade
<!-- resources/views/blog/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create New Blog Post</h1>
    
    <form action="{{ route('blog.store') }}" method="POST">
        @csrf
        
        <div class="form-group mb-4">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        
        <div class="form-group mb-4">
            <label for="content">Content</label>
            @livewire('ai-text-editor', [
                'content' => old('content', ''),
                'theme' => 'light'
            ])
        </div>
        
        <div class="form-check mb-4">
            <input type="checkbox" name="ai_generated" id="ai_generated" class="form-check-input">
            <label for="ai_generated" class="form-check-label">This content was AI generated</label>
        </div>
        
        <button type="submit" class="btn btn-primary">Publish Post</button>
    </form>
</div>
@endsection
```

### Email Template Editor

```php
// EmailTemplateController.php
class EmailTemplateController extends Controller
{
    public function edit(EmailTemplate $template)
    {
        return view('email-templates.edit', compact('template'));
    }
    
    public function generateWithAi(Request $request, AiService $aiService)
    {
        $request->validate([
            'prompt' => 'required|string',
            'provider' => 'required|string'
        ]);
        
        $result = $aiService->generate($request->prompt, $request->provider);
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'content' => $result['content']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 400);
    }
}
```

### Content Management System

```php
// ContentController.php
class ContentController extends Controller
{
    public function index(MemoryService $memoryService)
    {
        $recentVersions = $memoryService->getVersions(10);
        $stats = $memoryService->getStats();
        
        return view('content.index', compact('recentVersions', 'stats'));
    }
    
    public function restoreVersion(Request $request, MemoryService $memoryService)
    {
        $memory = $memoryService->restore($request->memory_id);
        
        if ($memory) {
            return response()->json([
                'success' => true,
                'content' => $memory->content
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Version not found'
        ], 404);
    }
}
```

## Customization

### Custom Theme

```css
/* resources/css/custom-editor.css */
.ai-text-editor.custom-theme {
    --primary-color: #6366f1;
    --secondary-color: #8b5cf6;
    --background-color: #ffffff;
    --text-color: #1f2937;
    --border-color: #e5e7eb;
    --hover-color: #f3f4f6;
}

.ai-text-editor.custom-theme.dark {
    --background-color: #111827;
    --text-color: #f9fafb;
    --border-color: #374151;
    --hover-color: #374151;
}

.ai-text-editor.custom-theme .toolbar-btn {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
}

.ai-text-editor.custom-theme .ai-btn {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
}
```

### Custom Livewire Component

```php
<?php

namespace App\Livewire;

use AiEditor\AiTextEditor\Livewire\AiTextEditor as BaseEditor;
use AiEditor\AiTextEditor\Services\AiService;
use AiEditor\AiTextEditor\Services\MemoryService;

class CustomAiEditor extends BaseEditor
{
    public $documentId;
    public $autoSave = true;
    public $saveInterval = 30; // seconds
    
    protected $listeners = [
        'documentUpdated' => 'handleDocumentUpdate',
        'autoSave' => 'performAutoSave'
    ];
    
    public function mount($documentId = null, $content = '', $theme = 'auto')
    {
        parent::mount($content, $theme);
        $this->documentId = $documentId;
        
        if ($this->autoSave) {
            $this->startAutoSave();
        }
    }
    
    public function handleDocumentUpdate($content)
    {
        $this->content = $content;
        
        if ($this->autoSave) {
            $this->performAutoSave();
        }
    }
    
    public function performAutoSave()
    {
        if ($this->documentId && $this->content) {
            // Save to your custom storage
            $this->saveToDocument();
        }
    }
    
    protected function saveToDocument()
    {
        // Your custom save logic
        \App\Models\Document::where('id', $this->documentId)
            ->update(['content' => $this->content]);
    }
    
    protected function startAutoSave()
    {
        $this->dispatch('startAutoSave', [
            'interval' => $this->saveInterval * 1000
        ]);
    }
}
```

### Custom JavaScript Integration

```javascript
// Custom editor integration
class CustomAiEditor {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            autoSave: true,
            saveInterval: 30000,
            onSave: null,
            onContentChange: null,
            ...options
        };
        
        this.init();
    }
    
    init() {
        // Initialize the editor
        this.editor = this.container.querySelector('#ai-editor');
        this.setupEventListeners();
        
        if (this.options.autoSave) {
            this.startAutoSave();
        }
    }
    
    setupEventListeners() {
        this.editor.addEventListener('input', (e) => {
            this.handleContentChange(e);
        });
        
        // Listen for AI actions
        this.container.addEventListener('aiAction', (e) => {
            this.handleAiAction(e.detail);
        });
    }
    
    handleContentChange(e) {
        const content = e.target.innerHTML;
        
        if (this.options.onContentChange) {
            this.options.onContentChange(content);
        }
        
        this.lastContent = content;
    }
    
    handleAiAction(detail) {
        console.log('AI Action:', detail);
        // Handle custom AI actions
    }
    
    startAutoSave() {
        setInterval(() => {
            if (this.lastContent && this.options.onSave) {
                this.options.onSave(this.lastContent);
            }
        }, this.options.saveInterval);
    }
    
    getContent() {
        return this.editor.innerHTML;
    }
    
    setContent(content) {
        this.editor.innerHTML = content;
    }
}

// Usage
const customEditor = new CustomAiEditor(
    document.querySelector('.ai-text-editor'),
    {
        autoSave: true,
        saveInterval: 30000,
        onSave: (content) => {
            // Save to your backend
            fetch('/api/save-content', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            });
        },
        onContentChange: (content) => {
            console.log('Content changed:', content);
        }
    }
);
```

## Best Practices

### 1. Performance Optimization

```php
// Use caching for AI responses
use Illuminate\Support\Facades\Cache;

class OptimizedAiService extends AiService
{
    public function generate(string $prompt, ?string $provider = null, array $options = []): array
    {
        $cacheKey = 'ai_generate_' . md5($prompt . $provider . serialize($options));
        
        return Cache::remember($cacheKey, 3600, function () use ($prompt, $provider, $options) {
            return parent::generate($prompt, $provider, $options);
        });
    }
}
```

### 2. Error Handling

```javascript
// Robust error handling
const handleAiRequest = async (endpoint, data) => {
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Unknown error occurred');
        }
        
        return result;
    } catch (error) {
        console.error('AI request failed:', error);
        showNotification('AI request failed: ' + error.message, 'error');
        throw error;
    }
};
```

### 3. Security Considerations

```php
// Rate limiting for AI requests
Route::middleware(['throttle:ai-requests'])->group(function () {
    Route::post('/ai-editor/generate', [AiController::class, 'generate']);
    Route::post('/ai-editor/edit', [AiController::class, 'edit']);
    // ... other AI routes
});
```

```php
// Custom validation
class AiRequestValidator
{
    public static function validateGenerateRequest(array $data): array
    {
        return [
            'prompt' => 'required|string|max:10000|min:10',
            'provider' => 'required|string|in:openai,anthropic,google,huggingface,openrouter',
            'options' => 'nullable|array',
            'options.max_tokens' => 'nullable|integer|min:1|max:4000',
            'options.temperature' => 'nullable|numeric|min:0|max:2',
        ];
    }
}
```

### 4. Testing

```php
// Feature test example
class AiTextEditorTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_ai_generate_endpoint()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson('/ai-editor/generate', [
                'prompt' => 'Write a short story',
                'provider' => 'openai'
            ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'content',
                'provider',
                'model'
            ]);
    }
    
    public function test_memory_system()
    {
        $user = User::factory()->create();
        $memoryService = app(MemoryService::class);
        
        $memory = $memoryService->store(
            'Test content',
            'test',
            'test-tag'
        );
        
        $this->assertDatabaseHas('editor_memories', [
            'id' => $memory->id,
            'user_id' => $user->id,
            'content' => 'Test content'
        ]);
    }
}
```

This comprehensive usage guide should help you get started with the AI Text Editor package and implement it effectively in your Laravel applications.
