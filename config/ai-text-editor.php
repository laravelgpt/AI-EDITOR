<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'default_provider' => env('AI_EDITOR_DEFAULT_PROVIDER', 'openai'),
        
        'providers' => [
            'openai' => [
                'api_key' => env('OPENAI_API_KEY'),
                'model' => env('OPENAI_MODEL', 'gpt-4'),
                'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            ],
            'anthropic' => [
                'api_key' => env('ANTHROPIC_API_KEY'),
                'model' => env('ANTHROPIC_MODEL', 'claude-3-sonnet-20240229'),
            ],
            'google' => [
                'api_key' => env('GOOGLE_API_KEY'),
                'model' => env('GOOGLE_MODEL', 'gemini-pro'),
            ],
            'huggingface' => [
                'api_key' => env('HUGGINGFACE_API_KEY'),
                'model' => env('HUGGINGFACE_MODEL', 'microsoft/DialoGPT-medium'),
                'base_url' => env('HUGGINGFACE_BASE_URL', 'https://api-inference.huggingface.co'),
            ],
            'openrouter' => [
                'api_key' => env('OPENROUTER_API_KEY'),
                'model' => env('OPENROUTER_MODEL', 'openai/gpt-4'),
                'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Editor Configuration
    |--------------------------------------------------------------------------
    */
    'editor' => [
        'theme' => env('AI_EDITOR_THEME', 'auto'), // auto, light, dark
        'default_height' => env('AI_EDITOR_HEIGHT', '400px'),
        'toolbar' => [
            'bold', 'italic', 'underline', 'strikethrough',
            '|', 'heading1', 'heading2', 'heading3',
            '|', 'bulletList', 'orderedList',
            '|', 'blockquote', 'codeBlock',
            '|', 'link', 'image', 'table',
            '|', 'undo', 'redo',
            '|', 'ai_generate', 'ai_edit', 'ai_summarize', 'ai_complete'
        ],
        'ai_button_position' => 'right', // left, right, center
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory System Configuration
    |--------------------------------------------------------------------------
    */
    'memory' => [
        'enabled' => env('AI_EDITOR_MEMORY_ENABLED', true),
        'max_versions' => env('AI_EDITOR_MAX_VERSIONS', 50),
        'auto_save' => env('AI_EDITOR_AUTO_SAVE', true),
        'save_interval' => env('AI_EDITOR_SAVE_INTERVAL', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'csrf_protection' => true,
        'rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'allowed_tags' => [
            'p', 'br', 'strong', 'em', 'u', 's', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'blockquote', 'code', 'pre', 'a', 'img', 'table',
            'thead', 'tbody', 'tr', 'th', 'td'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'animations' => true,
        'smooth_scroll' => true,
        'loading_indicators' => true,
        'notifications' => [
            'position' => 'top-right',
            'duration' => 5000,
        ],
    ],
];
