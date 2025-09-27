// AI Text Editor JavaScript
function aiTextEditor() {
    return {
        // State
        content: @json($content ?? ''),
        selectedText: '',
        aiPrompt: '',
        selectedProvider: @json($selectedProvider ?? 'openai'),
        showAiModal: false,
        showMemoryPanel: false,
        isLoading: false,
        theme: @json($theme ?? 'auto'),
        memories: @json($memories ?? []),
        searchQuery: '',
        selectedTag: '',
        aiAction: '',
        aiModalTitle: '',
        aiActionButtonText: '',

        // Initialize
        init() {
            this.setupTheme();
            this.setupEditor();
            this.setupEventListeners();
        },

        // Theme Management
        setupTheme() {
            if (this.theme === 'auto') {
                this.theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            this.applyTheme();
        },

        applyTheme() {
            const editor = this.$el.querySelector('.ai-text-editor');
            if (this.theme === 'dark') {
                editor.classList.add('dark');
            } else {
                editor.classList.remove('dark');
            }
        },

        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            this.applyTheme();
        },

        // Editor Setup
        setupEditor() {
            const editor = this.$el.querySelector('#ai-editor');
            if (editor) {
                editor.innerHTML = this.content;
            }
        },

        setupEventListeners() {
            // Listen for Livewire events
            this.$wire.on('contentUpdated', (content) => {
                this.content = content;
                this.updateEditorContent();
            });

            // Listen for text selection
            document.addEventListener('selectionchange', () => {
                this.handleTextSelection();
            });
        },

        // Content Management
        updateContent(event) {
            this.content = event.target.innerHTML;
            this.$wire.set('content', this.content);
        },

        updateEditorContent() {
            const editor = this.$el.querySelector('#ai-editor');
            if (editor && editor.innerHTML !== this.content) {
                editor.innerHTML = this.content;
            }
        },

        handleTextSelection() {
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                this.selectedText = selection.toString().trim();
            }
        },

        // Formatting Functions
        toggleBold() {
            document.execCommand('bold');
            this.focusEditor();
        },

        toggleItalic() {
            document.execCommand('italic');
            this.focusEditor();
        },

        toggleUnderline() {
            document.execCommand('underline');
            this.focusEditor();
        },

        formatHeading(level) {
            document.execCommand('formatBlock', false, `h${level}`);
            this.focusEditor();
        },

        insertList(type) {
            if (type === 'bullet') {
                document.execCommand('insertUnorderedList');
            } else {
                document.execCommand('insertOrderedList');
            }
            this.focusEditor();
        },

        insertLink() {
            const url = prompt('Enter URL:');
            if (url) {
                document.execCommand('createLink', false, url);
            }
            this.focusEditor();
        },

        insertImage() {
            const url = prompt('Enter image URL:');
            if (url) {
                const img = document.createElement('img');
                img.src = url;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                document.execCommand('insertHTML', false, img.outerHTML);
            }
            this.focusEditor();
        },

        undo() {
            document.execCommand('undo');
            this.focusEditor();
        },

        redo() {
            document.execCommand('redo');
            this.focusEditor();
        },

        focusEditor() {
            const editor = this.$el.querySelector('#ai-editor');
            if (editor) {
                editor.focus();
            }
        },

        // AI Functions
        openAiModal(action) {
            this.aiAction = action;
            this.showAiModal = true;
            
            switch (action) {
                case 'generate':
                    this.aiModalTitle = 'AI Generate';
                    this.aiActionButtonText = 'Generate';
                    this.aiPrompt = '';
                    break;
                case 'edit':
                    this.aiModalTitle = 'AI Edit';
                    this.aiActionButtonText = 'Edit';
                    this.aiPrompt = 'Please edit the following text:';
                    break;
                case 'summarize':
                    this.aiModalTitle = 'AI Summarize';
                    this.aiActionButtonText = 'Summarize';
                    this.aiPrompt = 'Please summarize the following text:';
                    break;
                case 'complete':
                    this.aiModalTitle = 'AI Complete';
                    this.aiActionButtonText = 'Complete';
                    this.aiPrompt = 'Please complete the following text:';
                    break;
            }
        },

        executeAiAction() {
            if (!this.aiPrompt.trim()) {
                alert('Please enter a prompt.');
                return;
            }

            this.isLoading = true;

            switch (this.aiAction) {
                case 'generate':
                    this.$wire.generateContent();
                    break;
                case 'edit':
                    this.$wire.editContent();
                    break;
                case 'summarize':
                    this.$wire.summarizeContent();
                    break;
                case 'complete':
                    this.$wire.completeContent();
                    break;
            }
        },

        closeAiModal() {
            this.showAiModal = false;
            this.aiPrompt = '';
            this.selectedText = '';
            this.isLoading = false;
        },

        // Memory Functions
        toggleMemoryPanel() {
            this.showMemoryPanel = !this.showMemoryPanel;
            if (this.showMemoryPanel) {
                this.$wire.loadMemories();
            }
        },

        searchMemories() {
            this.$wire.searchMemories();
        },

        restoreMemory(id) {
            this.$wire.restoreFromMemory(id);
        },

        // Utility Functions
        getAvailableProviders() {
            return this.$wire.getAvailableProviders();
        }
    };
}

// Global functions for external access
window.aiTextEditor = {
    // Initialize editor
    init: function(container) {
        if (typeof Alpine !== 'undefined') {
            Alpine.data('aiTextEditor', aiTextEditor);
        }
    },

    // Get editor content
    getContent: function() {
        const editor = document.querySelector('#ai-editor');
        return editor ? editor.innerHTML : '';
    },

    // Set editor content
    setContent: function(content) {
        const editor = document.querySelector('#ai-editor');
        if (editor) {
            editor.innerHTML = content;
        }
    },

    // Focus editor
    focus: function() {
        const editor = document.querySelector('#ai-editor');
        if (editor) {
            editor.focus();
        }
    },

    // Get selected text
    getSelectedText: function() {
        const selection = window.getSelection();
        return selection.toString().trim();
    },

    // Insert text at cursor
    insertText: function(text) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(document.createTextNode(text));
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    },

    // Insert HTML at cursor
    insertHTML: function(html) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const fragment = document.createDocumentFragment();
            while (tempDiv.firstChild) {
                fragment.appendChild(tempDiv.firstChild);
            }
            range.insertNode(fragment);
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }
};

// Auto-initialize if Alpine is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Alpine !== 'undefined') {
        Alpine.data('aiTextEditor', aiTextEditor);
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = aiTextEditor;
}
