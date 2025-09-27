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
    | Frontend Stacks Configuration
    |--------------------------------------------------------------------------
    */
    'stacks' => [
        'blade-livewire' => [
            'name' => 'Blade + Livewire',
            'description' => 'Traditional Laravel with Blade templates and Livewire components',
            'dependencies' => [
                'composer' => [
                    'livewire/livewire',
                    'laravel/breeze',
                ],
                'npm' => [
                    '@tailwindcss/forms',
                    'alpinejs',
                ],
            ],
            'features' => [
                'Server-side rendering',
                'Real-time updates',
                'Form handling',
                'Authentication',
            ],
        ],
        'vue-spa' => [
            'name' => 'Vue.js SPA',
            'description' => 'Single Page Application with Vue.js frontend',
            'dependencies' => [
                'composer' => [
                    'laravel/sanctum',
                    'laravel/breeze',
                ],
                'npm' => [
                    'vue@^3.0',
                    'vue-router@^4.0',
                    'pinia@^2.0',
                    'axios',
                    '@vitejs/plugin-vue',
                ],
            ],
            'features' => [
                'Client-side routing',
                'State management',
                'API integration',
                'Component-based architecture',
            ],
        ],
        'react-nextjs' => [
            'name' => 'React + Next.js',
            'description' => 'React frontend with Next.js framework',
            'dependencies' => [
                'composer' => [
                    'laravel/sanctum',
                    'laravel/breeze',
                ],
                'npm' => [
                    'react@^18.0',
                    'react-dom@^18.0',
                    'next@^14.0',
                    '@types/react',
                    '@types/react-dom',
                ],
            ],
            'features' => [
                'Server-side rendering',
                'Static site generation',
                'API routes',
                'Component-based architecture',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Themes Configuration
    |--------------------------------------------------------------------------
    */
    'themes' => [
        'default' => [
            'name' => 'Default Theme',
            'description' => 'Clean and professional default theme',
            'colors' => [
                'primary' => '#3B82F6',
                'secondary' => '#6B7280',
                'success' => '#10B981',
                'warning' => '#F59E0B',
                'error' => '#EF4444',
            ],
            'features' => [
                'Responsive design',
                'Dark mode support',
                'Accessibility compliant',
            ],
        ],
        'dark' => [
            'name' => 'Dark Theme',
            'description' => 'Modern dark theme for better night viewing',
            'colors' => [
                'primary' => '#60A5FA',
                'secondary' => '#9CA3AF',
                'success' => '#34D399',
                'warning' => '#FBBF24',
                'error' => '#F87171',
            ],
            'features' => [
                'Dark mode optimized',
                'Reduced eye strain',
                'Modern aesthetics',
            ],
        ],
        'minimal' => [
            'name' => 'Minimal Theme',
            'description' => 'Clean and minimal design with focus on content',
            'colors' => [
                'primary' => '#000000',
                'secondary' => '#6B7280',
                'success' => '#059669',
                'warning' => '#D97706',
                'error' => '#DC2626',
            ],
            'features' => [
                'Minimal design',
                'Content focused',
                'Fast loading',
            ],
        ],
        'colorful' => [
            'name' => 'Colorful Theme',
            'description' => 'Vibrant and colorful theme for creative projects',
            'colors' => [
                'primary' => '#8B5CF6',
                'secondary' => '#EC4899',
                'success' => '#10B981',
                'warning' => '#F59E0B',
                'error' => '#EF4444',
            ],
            'features' => [
                'Vibrant colors',
                'Creative design',
                'Engaging interface',
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
