<?php

namespace AiEditor\AiTextEditor\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use AiEditor\AiTextEditor\Services\AiService;
use AiEditor\AiTextEditor\Services\MemoryService;

class AiTextEditor extends Component
{
    use WithFileUploads;

    public string $content = '';
    public string $selectedText = '';
    public string $aiPrompt = '';
    public string $selectedProvider = '';
    public bool $showAiModal = false;
    public bool $showMemoryPanel = false;
    public bool $isLoading = false;
    public string $theme = 'auto';
    public array $memories = [];
    public string $searchQuery = '';
    public string $selectedTag = '';

    protected $listeners = [
        'textSelected' => 'handleTextSelection',
        'aiAction' => 'handleAiAction',
        'restoreMemory' => 'restoreFromMemory',
    ];

    public function mount(string $content = '', string $theme = 'auto'): void
    {
        $this->content = $content;
        $this->theme = $theme;
        $this->selectedProvider = config('ai-text-editor.ai.default_provider', 'openai');
        $this->loadMemories();
    }

    public function handleTextSelection(string $text): void
    {
        $this->selectedText = $text;
    }

    public function handleAiAction(string $action): void
    {
        $this->showAiModal = true;
        
        switch ($action) {
            case 'generate':
                $this->aiPrompt = '';
                break;
            case 'edit':
                $this->aiPrompt = 'Please edit the following text:';
                break;
            case 'summarize':
                $this->aiPrompt = 'Please summarize the following text:';
                break;
            case 'complete':
                $this->aiPrompt = 'Please complete the following text:';
                break;
        }
    }

    public function generateContent(): void
    {
        $this->validate([
            'aiPrompt' => 'required|string|max:10000',
            'selectedProvider' => 'required|string',
        ]);

        $this->isLoading = true;

        try {
            $prompt = $this->aiPrompt;
            if ($this->selectedText) {
                $prompt .= "\n\nSelected text:\n" . $this->selectedText;
            }

            $result = app(AiService::class)->generate($prompt, $this->selectedProvider);

            if ($result['success']) {
                $this->content = $result['content'];
                $this->dispatch('contentUpdated', $this->content);
                $this->showAiModal = false;
                $this->aiPrompt = '';
                $this->selectedText = '';
                
                session()->flash('success', 'Content generated successfully!');
            } else {
                session()->flash('error', 'Failed to generate content: ' . $result['error']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function editContent(): void
    {
        if (!$this->selectedText) {
            session()->flash('error', 'Please select some text to edit.');
            return;
        }

        $this->validate([
            'aiPrompt' => 'required|string|max:2000',
            'selectedProvider' => 'required|string',
        ]);

        $this->isLoading = true;

        try {
            $result = app(AiService::class)->edit(
                $this->selectedText,
                $this->aiPrompt,
                $this->selectedProvider
            );

            if ($result['success']) {
                // Replace selected text with edited content
                $this->content = str_replace($this->selectedText, $result['content'], $this->content);
                $this->dispatch('contentUpdated', $this->content);
                $this->showAiModal = false;
                $this->aiPrompt = '';
                $this->selectedText = '';
                
                session()->flash('success', 'Text edited successfully!');
            } else {
                session()->flash('error', 'Failed to edit text: ' . $result['error']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function summarizeContent(): void
    {
        if (!$this->selectedText) {
            session()->flash('error', 'Please select some text to summarize.');
            return;
        }

        $this->isLoading = true;

        try {
            $result = app(AiService::class)->summarize(
                $this->selectedText,
                $this->selectedProvider
            );

            if ($result['success']) {
                $this->content = str_replace($this->selectedText, $result['content'], $this->content);
                $this->dispatch('contentUpdated', $this->content);
                $this->showAiModal = false;
                $this->selectedText = '';
                
                session()->flash('success', 'Text summarized successfully!');
            } else {
                session()->flash('error', 'Failed to summarize text: ' . $result['error']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function completeContent(): void
    {
        if (!$this->selectedText) {
            session()->flash('error', 'Please select some text to complete.');
            return;
        }

        $this->isLoading = true;

        try {
            $result = app(AiService::class)->complete(
                $this->selectedText,
                $this->selectedProvider
            );

            if ($result['success']) {
                $this->content = str_replace($this->selectedText, $result['content'], $this->content);
                $this->dispatch('contentUpdated', $this->content);
                $this->showAiModal = false;
                $this->selectedText = '';
                
                session()->flash('success', 'Text completed successfully!');
            } else {
                session()->flash('error', 'Failed to complete text: ' . $result['error']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function toggleMemoryPanel(): void
    {
        $this->showMemoryPanel = !$this->showMemoryPanel;
        
        if ($this->showMemoryPanel) {
            $this->loadMemories();
        }
    }

    public function loadMemories(): void
    {
        $this->memories = app(MemoryService::class)->getVersions(20, $this->selectedTag ?: null)
            ->map(function ($memory) {
                return [
                    'id' => $memory->id,
                    'content_preview' => $memory->content_preview,
                    'action' => $memory->action,
                    'tag' => $memory->tag,
                    'formatted_date' => $memory->formatted_date,
                    'relative_date' => $memory->relative_date,
                ];
            })
            ->toArray();
    }

    public function restoreFromMemory(int $id): void
    {
        try {
            $memory = app(MemoryService::class)->restore($id);
            
            if ($memory) {
                $this->content = $memory->content;
                $this->dispatch('contentUpdated', $this->content);
                session()->flash('success', 'Content restored from memory!');
            } else {
                session()->flash('error', 'Memory not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function searchMemories(): void
    {
        if (empty($this->searchQuery)) {
            $this->loadMemories();
            return;
        }

        $this->memories = app(MemoryService::class)->search($this->searchQuery, $this->selectedTag ?: null)
            ->map(function ($memory) {
                return [
                    'id' => $memory->id,
                    'content_preview' => $memory->content_preview,
                    'action' => $memory->action,
                    'tag' => $memory->tag,
                    'formatted_date' => $memory->formatted_date,
                    'relative_date' => $memory->relative_date,
                ];
            })
            ->toArray();
    }

    public function closeAiModal(): void
    {
        $this->showAiModal = false;
        $this->aiPrompt = '';
        $this->selectedText = '';
    }

    public function getAvailableProviders(): array
    {
        return app(AiService::class)->getAvailableProviders();
    }

    public function render()
    {
        return view('ai-text-editor::livewire.ai-text-editor', [
            'availableProviders' => $this->getAvailableProviders(),
        ]);
    }
}
