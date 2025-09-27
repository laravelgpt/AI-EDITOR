<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-Stack Configuration
    |--------------------------------------------------------------------------
    */
    'version' => '1.0.0',
    'debug' => env('MULTI_STACK_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Available Stacks
    |--------------------------------------------------------------------------
    */
    'stacks' => [
        'blade-livewire' => [
            'name' => 'Blade + Livewire',
            'description' => 'Traditional Laravel with Blade templates and Livewire for real-time updates',
            'icon' => '⚡',
            'color' => 'blue',
            'dependencies' => [
                'composer' => [
                    'livewire/livewire',
                    'spatie/laravel-permission',
                    'laravel/breeze'
                ],
                'npm' => [
                    'alpinejs',
                    'tailwindcss',
                    '@tailwindcss/forms',
                    '@tailwindcss/typography',
                    'axios'
                ]
            ],
            'features' => [
                'real_time_updates',
                'server_side_rendering',
                'blade_components',
                'livewire_components',
                'alpine_js',
                'tailwind_css'
            ],
            'routes' => [
                'web' => true,
                'api' => false,
                'spa' => false
            ]
        ],
        'vue-spa' => [
            'name' => 'Vue.js SPA',
            'description' => 'Single Page Application with Vue.js frontend and Laravel API backend',
            'icon' => '💚',
            'color' => 'green',
            'dependencies' => [
                'composer' => [
                    'laravel/sanctum',
                    'spatie/laravel-permission',
                    'laravel/breeze'
                ],
                'npm' => [
                    'vue@3',
                    'vue-router@4',
                    'pinia',
                    'axios',
                    'tailwindcss',
                    '@tailwindcss/forms',
                    'vite',
                    'laravel-vite-plugin'
                ]
            ],
            'features' => [
                'spa_routing',
                'state_management',
                'api_integration',
                'client_side_rendering',
                'vue_components',
                'pinia_store'
            ],
            'routes' => [
                'web' => false,
                'api' => true,
                'spa' => true
            ]
        ],
        'react-nextjs' => [
            'name' => 'React + Next.js',
            'description' => 'React frontend with Next.js for SSR and Laravel API backend',
            'icon' => '⚛️',
            'color' => 'purple',
            'dependencies' => [
                'composer' => [
                    'laravel/sanctum',
                    'spatie/laravel-permission',
                    'laravel/breeze'
                ],
                'npm' => [
                    'next@14',
                    'react@18',
                    'react-dom@18',
                    'axios',
                    'tailwindcss',
                    '@tailwindcss/forms',
                    'typescript',
                    '@types/react',
                    '@types/node'
                ]
            ],
            'features' => [
                'ssr_support',
                'api_routes',
                'static_generation',
                'react_components',
                'typescript_support',
                'next_router'
            ],
            'routes' => [
                'web' => false,
                'api' => true,
                'spa' => true
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation Configuration
    |--------------------------------------------------------------------------
    */
    'installer' => [
        'auto_install_dependencies' => env('MULTI_STACK_AUTO_INSTALL', true),
        'install_npm_dependencies' => env('MULTI_STACK_INSTALL_NPM', true),
        'install_composer_dependencies' => env('MULTI_STACK_INSTALL_COMPOSER', true),
        'run_migrations' => env('MULTI_STACK_RUN_MIGRATIONS', true),
        'seed_database' => env('MULTI_STACK_SEED_DATABASE', true),
        'setup_authentication' => env('MULTI_STACK_SETUP_AUTH', true),
        'create_admin_user' => env('MULTI_STACK_CREATE_ADMIN', true),
        'setup_theme' => env('MULTI_STACK_SETUP_THEME', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    */
    'themes' => [
        'default' => [
            'name' => 'Default',
            'description' => 'Clean and modern default theme',
            'colors' => [
                'primary' => '#3b82f6',
                'secondary' => '#6b7280',
                'accent' => '#f59e0b',
                'background' => '#ffffff',
                'surface' => '#f9fafb',
                'text' => '#111827'
            ]
        ],
        'dark' => [
            'name' => 'Dark',
            'description' => 'Dark theme with modern aesthetics',
            'colors' => [
                'primary' => '#60a5fa',
                'secondary' => '#9ca3af',
                'accent' => '#fbbf24',
                'background' => '#111827',
                'surface' => '#1f2937',
                'text' => '#f9fafb'
            ]
        ],
        'minimal' => [
            'name' => 'Minimal',
            'description' => 'Minimalist design with clean lines',
            'colors' => [
                'primary' => '#000000',
                'secondary' => '#6b7280',
                'accent' => '#ef4444',
                'background' => '#ffffff',
                'surface' => '#ffffff',
                'text' => '#000000'
            ]
        ],
        'colorful' => [
            'name' => 'Colorful',
            'description' => 'Vibrant theme with multiple colors',
            'colors' => [
                'primary' => '#8b5cf6',
                'secondary' => '#06b6d4',
                'accent' => '#f59e0b',
                'background' => '#fef3c7',
                'surface' => '#ffffff',
                'text' => '#1f2937'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Configuration
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'enabled' => true,
        'driver' => 'session',
        'features' => [
            'registration' => true,
            'email_verification' => true,
            'password_reset' => true,
            'two_factor' => false,
            'social_login' => false,
        ],
        'guards' => [
            'web' => ['driver' => 'session'],
            'api' => ['driver' => 'sanctum'],
        ],
        'roles' => [
            'admin' => 'Administrator',
            'user' => 'User',
            'moderator' => 'Moderator'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Starter Pages Configuration
    |--------------------------------------------------------------------------
    */
    'pages' => [
        'dashboard' => [
            'enabled' => true,
            'features' => ['stats', 'charts', 'recent_activity', 'quick_actions']
        ],
        'profile' => [
            'enabled' => true,
            'features' => ['avatar', 'personal_info', 'security_settings', 'preferences']
        ],
        'settings' => [
            'enabled' => true,
            'features' => ['general', 'notifications', 'privacy', 'advanced']
        ],
        'users' => [
            'enabled' => true,
            'features' => ['list', 'create', 'edit', 'delete', 'roles']
        ],
        'posts' => [
            'enabled' => true,
            'features' => ['list', 'create', 'edit', 'delete', 'categories', 'tags']
        ]
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
        'api_rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 100,
            'decay_minutes' => 1,
        ],
        'input_validation' => true,
        'xss_protection' => true,
        'sql_injection_protection' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'caching' => [
            'enabled' => true,
            'driver' => 'file',
            'ttl' => 3600,
        ],
        'asset_optimization' => true,
        'database_optimization' => true,
        'queue_processing' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Tools
    |--------------------------------------------------------------------------
    */
    'dev_tools' => [
        'debug_toolbar' => env('MULTI_STACK_DEBUG_TOOLBAR', false),
        'api_documentation' => env('MULTI_STACK_API_DOCS', true),
        'testing_setup' => env('MULTI_STACK_TESTING', true),
        'linting' => env('MULTI_STACK_LINTING', true),
    ]
];