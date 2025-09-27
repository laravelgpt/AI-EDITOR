<div class="ai-text-editor" x-data="aiTextEditor()" x-init="init()">
    <!-- Editor Container -->
    <div class="editor-container" :class="{ 'dark': theme === 'dark' }">
        <!-- Toolbar -->
        <div class="editor-toolbar">
            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" @click="toggleBold()" title="Bold">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a1 1 0 011-1h5.5a2.5 2.5 0 011.5 4.5 2.5 2.5 0 011.5 4.5H6a1 1 0 01-1-1V4z"/>
                    </svg>
                </button>
                <button type="button" class="toolbar-btn" @click="toggleItalic()" title="Italic">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 3a1 1 0 000 2h1.5a1.5 1.5 0 011.5 1.5v1H8a1 1 0 000 2h3v1a1.5 1.5 0 01-1.5 1.5H8a1 1 0 000 2h1.5a3.5 3.5 0 003.5-3.5V6.5A3.5 3.5 0 009.5 3H8z"/>
                    </svg>
                </button>
                <button type="button" class="toolbar-btn" @click="toggleUnderline()" title="Underline">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                    </svg>
                </button>
            </div>

            <div class="toolbar-separator"></div>

            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" @click="formatHeading(1)" title="Heading 1">H1</button>
                <button type="button" class="toolbar-btn" @click="formatHeading(2)" title="Heading 2">H2</button>
                <button type="button" class="toolbar-btn" @click="formatHeading(3)" title="Heading 3">H3</button>
            </div>

            <div class="toolbar-separator"></div>

            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" @click="insertList('bullet')" title="Bullet List">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 000 2h.01a1 1 0 100-2H3zm0 4a1 1 0 000 2h.01a1 1 0 100-2H3zm0 4a1 1 0 000 2h.01a1 1 0 100-2H3zm4-8a1 1 0 011-1h9a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h9a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h9a1 1 0 110 2H8a1 1 0 01-1-1z"/>
                    </svg>
                </button>
                <button type="button" class="toolbar-btn" @click="insertList('ordered')" title="Numbered List">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 000 2h.01a1 1 0 100-2H3zm0 4a1 1 0 000 2h.01a1 1 0 100-2H3zm0 4a1 1 0 000 2h.01a1 1 0 100-2H3zm4-8a1 1 0 011-1h9a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h9a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h9a1 1 0 110 2H8a1 1 0 01-1-1z"/>
                    </svg>
                </button>
            </div>

            <div class="toolbar-separator"></div>

            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" @click="insertLink()" title="Insert Link">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-3 3a4 4 0 00.5 6.5 1 1 0 00-1.414-1.414 2 2 0 01-.5-2.828l3-3z"/>
                    </svg>
                </button>
                <button type="button" class="toolbar-btn" @click="insertImage()" title="Insert Image">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                    </svg>
                </button>
            </div>

            <div class="toolbar-separator"></div>

            <!-- AI Actions -->
            <div class="toolbar-group ai-actions">
                <button type="button" class="toolbar-btn ai-btn" @click="openAiModal('generate')" title="AI Generate">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    AI Generate
                </button>
                <button type="button" class="toolbar-btn ai-btn" @click="openAiModal('edit')" title="AI Edit">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    AI Edit
                </button>
                <button type="button" class="toolbar-btn ai-btn" @click="openAiModal('summarize')" title="AI Summarize">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 1a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    AI Summarize
                </button>
                <button type="button" class="toolbar-btn ai-btn" @click="openAiModal('complete')" title="AI Complete">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 1a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    AI Complete
                </button>
            </div>

            <div class="toolbar-separator"></div>

            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" @click="undo()" title="Undo">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <button type="button" class="toolbar-btn" @click="redo()" title="Redo">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div class="toolbar-spacer"></div>

            <div class="toolbar-group">
                <button type="button" class="toolbar-btn" @click="toggleMemoryPanel()" title="Memory">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                    </svg>
                </button>
                <button type="button" class="toolbar-btn" @click="toggleTheme()" title="Toggle Theme">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Editor Content -->
        <div class="editor-content">
            <div 
                id="ai-editor" 
                class="editor-body"
                contenteditable="true"
                @input="updateContent($event)"
                @select="handleTextSelection($event)"
                x-html="content"
            ></div>
        </div>
    </div>

    <!-- AI Modal -->
    <div x-show="showAiModal" x-transition class="ai-modal-overlay" @click="closeAiModal()">
        <div class="ai-modal" @click.stop>
            <div class="ai-modal-header">
                <h3 x-text="aiModalTitle"></h3>
                <button @click="closeAiModal()" class="close-btn">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            
            <div class="ai-modal-body">
                <div class="form-group">
                    <label for="ai-prompt">Prompt:</label>
                    <textarea 
                        id="ai-prompt"
                        x-model="aiPrompt" 
                        class="form-control"
                        rows="4"
                        placeholder="Enter your prompt here..."
                    ></textarea>
                </div>
                
                <div class="form-group">
                    <label for="ai-provider">AI Provider:</label>
                    <select id="ai-provider" x-model="selectedProvider" class="form-control">
                        @foreach($availableProviders as $provider)
                            <option value="{{ $provider }}">{{ ucfirst($provider) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="ai-modal-footer">
                <button @click="closeAiModal()" class="btn btn-secondary">Cancel</button>
                <button @click="executeAiAction()" class="btn btn-primary" :disabled="isLoading">
                    <span x-show="!isLoading" x-text="aiActionButtonText"></span>
                    <span x-show="isLoading">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Memory Panel -->
    <div x-show="showMemoryPanel" x-transition class="memory-panel">
        <div class="memory-panel-header">
            <h3>Memory</h3>
            <button @click="toggleMemoryPanel()" class="close-btn">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
        
        <div class="memory-panel-body">
            <div class="memory-search">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    @input="searchMemories()"
                    placeholder="Search memories..."
                    class="form-control"
                >
            </div>
            
            <div class="memory-list">
                <template x-for="memory in memories" :key="memory.id">
                    <div class="memory-item">
                        <div class="memory-preview" x-text="memory.content_preview"></div>
                        <div class="memory-meta">
                            <span class="memory-action" x-text="memory.action"></span>
                            <span class="memory-date" x-text="memory.relative_date"></span>
                        </div>
                        <div class="memory-actions">
                            <button @click="restoreMemory(memory.id)" class="btn btn-sm btn-primary">Restore</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="isLoading" class="loading-overlay">
        <div class="loading-spinner">
            <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Processing with AI...</span>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/ai-text-editor/css/ai-text-editor.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('vendor/ai-text-editor/js/ai-text-editor.js') }}"></script>
@endpush
