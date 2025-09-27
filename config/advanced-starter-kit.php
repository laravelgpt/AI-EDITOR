<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Advanced Starter Kit Configuration
    |--------------------------------------------------------------------------
    */
    'version' => '1.0.0',
    'debug' => env('ADVANCED_STARTER_KIT_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | AI Configuration
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'enabled' => env('ADVANCED_STARTER_KIT_AI_ENABLED', true),
        'default_provider' => env('ADVANCED_STARTER_KIT_AI_PROVIDER', 'openai'),
        
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
    | Feature System Configuration
    |--------------------------------------------------------------------------
    */
    'features' => [
        'auto_register' => true,
        'cache_enabled' => true,
        'cache_ttl' => 3600, // 1 hour
        'max_features' => 1000,
        'feature_categories' => [
            'authentication' => 'Authentication & Security',
            'content_management' => 'Content Management',
            'ecommerce' => 'E-commerce',
            'communication' => 'Communication',
            'analytics' => 'Analytics & Reporting',
            'integration' => 'Integrations',
            'ui_components' => 'UI Components',
            'utilities' => 'Utilities',
            'ai_features' => 'AI Features',
            'custom' => 'Custom Features',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Stack Configuration
    |--------------------------------------------------------------------------
    */
    'stacks' => [
        'blade-livewire' => [
            'name' => 'Blade + Livewire',
            'description' => 'Traditional Laravel with Blade templates and Livewire for real-time updates',
            'dependencies' => [
                'composer' => ['livewire/livewire', 'spatie/laravel-permission'],
                'npm' => ['alpinejs', 'tailwindcss', '@tailwindcss/forms', '@tailwindcss/typography']
            ],
            'features' => [
                'real_time_updates',
                'server_side_rendering',
                'blade_components',
                'livewire_components',
                'dynamic_routing'
            ]
        ],
        'vue-spa' => [
            'name' => 'Vue.js SPA',
            'description' => 'Single Page Application with Vue.js frontend and Laravel API',
            'dependencies' => [
                'composer' => ['laravel/sanctum', 'spatie/laravel-permission'],
                'npm' => ['vue@3', 'vue-router@4', 'pinia', 'axios', 'tailwindcss', '@tailwindcss/forms']
            ],
            'features' => [
                'spa_routing',
                'state_management',
                'api_integration',
                'client_side_rendering',
                'dynamic_components'
            ]
        ],
        'react-nextjs' => [
            'name' => 'React + Next.js',
            'description' => 'React frontend with Next.js for SSR and Laravel API backend',
            'dependencies' => [
                'composer' => ['laravel/sanctum', 'spatie/laravel-permission'],
                'npm' => ['next@14', 'react@18', 'react-dom@18', 'axios', 'tailwindcss', '@tailwindcss/forms']
            ],
            'features' => [
                'ssr_support',
                'api_routes',
                'static_generation',
                'react_components',
                'dynamic_imports'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Feature Categories
    |--------------------------------------------------------------------------
    */
    'feature_categories' => [
        'authentication' => [
            'name' => 'Authentication & Security',
            'icon' => 'shield-check',
            'color' => 'blue',
            'features' => [
                'user_registration',
                'user_login',
                'password_reset',
                'email_verification',
                'two_factor_authentication',
                'social_login',
                'role_permissions',
                'api_authentication',
                'session_management',
                'security_logs'
            ]
        ],
        'content_management' => [
            'name' => 'Content Management',
            'icon' => 'document-text',
            'color' => 'green',
            'features' => [
                'blog_system',
                'page_builder',
                'media_library',
                'file_manager',
                'content_scheduling',
                'seo_management',
                'content_versions',
                'content_approval',
                'content_templates',
                'content_analytics'
            ]
        ],
        'ecommerce' => [
            'name' => 'E-commerce',
            'icon' => 'shopping-cart',
            'color' => 'purple',
            'features' => [
                'product_catalog',
                'shopping_cart',
                'checkout_process',
                'payment_integration',
                'order_management',
                'inventory_management',
                'customer_management',
                'discount_system',
                'shipping_calculator',
                'sales_analytics'
            ]
        ],
        'communication' => [
            'name' => 'Communication',
            'icon' => 'chat-bubble-left-right',
            'color' => 'yellow',
            'features' => [
                'messaging_system',
                'email_notifications',
                'push_notifications',
                'chat_system',
                'comment_system',
                'forum_system',
                'newsletter_system',
                'contact_forms',
                'support_tickets',
                'real_time_chat'
            ]
        ],
        'analytics' => [
            'name' => 'Analytics & Reporting',
            'icon' => 'chart-bar',
            'color' => 'red',
            'features' => [
                'user_analytics',
                'content_analytics',
                'sales_reports',
                'performance_metrics',
                'custom_dashboards',
                'data_visualization',
                'export_reports',
                'scheduled_reports',
                'real_time_analytics',
                'predictive_analytics'
            ]
        ],
        'integration' => [
            'name' => 'Integrations',
            'icon' => 'puzzle-piece',
            'color' => 'indigo',
            'features' => [
                'api_integrations',
                'webhook_system',
                'third_party_services',
                'social_media_integration',
                'payment_gateways',
                'email_services',
                'cloud_storage',
                'database_sync',
                'external_apis',
                'custom_integrations'
            ]
        ],
        'ui_components' => [
            'name' => 'UI Components',
            'icon' => 'squares-2x2',
            'color' => 'pink',
            'features' => [
                'form_builder',
                'data_tables',
                'modal_system',
                'notification_system',
                'loading_states',
                'progress_indicators',
                'charts_graphs',
                'calendar_system',
                'file_uploader',
                'rich_text_editor'
            ]
        ],
        'utilities' => [
            'name' => 'Utilities',
            'icon' => 'wrench-screwdriver',
            'color' => 'gray',
            'features' => [
                'cron_jobs',
                'backup_system',
                'log_viewer',
                'system_monitoring',
                'cache_management',
                'queue_management',
                'database_tools',
                'file_management',
                'system_settings',
                'maintenance_mode'
            ]
        ],
        'ai_features' => [
            'name' => 'AI Features',
            'icon' => 'sparkles',
            'color' => 'emerald',
            'features' => [
                'ai_text_generation',
                'ai_content_optimization',
                'ai_image_generation',
                'ai_chatbot',
                'ai_translation',
                'ai_sentiment_analysis',
                'ai_recommendations',
                'ai_automation',
                'ai_insights',
                'ai_personalization'
            ]
        ],
        'custom' => [
            'name' => 'Custom Features',
            'icon' => 'code-bracket',
            'color' => 'orange',
            'features' => [
                'custom_models',
                'custom_controllers',
                'custom_views',
                'custom_api_endpoints',
                'custom_middleware',
                'custom_commands',
                'custom_jobs',
                'custom_events',
                'custom_listeners',
                'custom_services'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation Configuration
    |--------------------------------------------------------------------------
    */
    'installer' => [
        'auto_install_dependencies' => env('ADVANCED_STARTER_KIT_AUTO_INSTALL', true),
        'install_npm_dependencies' => env('ADVANCED_STARTER_KIT_INSTALL_NPM', true),
        'install_composer_dependencies' => env('ADVANCED_STARTER_KIT_INSTALL_COMPOSER', true),
        'run_migrations' => env('ADVANCED_STARTER_KIT_RUN_MIGRATIONS', true),
        'seed_database' => env('ADVANCED_STARTER_KIT_SEED_DATABASE', true),
        'setup_ai' => env('ADVANCED_STARTER_KIT_SETUP_AI', true),
        'create_admin_user' => env('ADVANCED_STARTER_KIT_CREATE_ADMIN', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    */
    'theme' => [
        'default' => 'light',
        'support_dark_mode' => true,
        'color_schemes' => [
            'blue' => ['primary' => '#3b82f6', 'secondary' => '#1e40af'],
            'green' => ['primary' => '#10b981', 'secondary' => '#059669'],
            'purple' => ['primary' => '#8b5cf6', 'secondary' => '#7c3aed'],
            'red' => ['primary' => '#ef4444', 'secondary' => '#dc2626'],
            'yellow' => ['primary' => '#f59e0b', 'secondary' => '#d97706'],
        ],
        'components' => [
            'buttons' => true,
            'forms' => true,
            'modals' => true,
            'tables' => true,
            'cards' => true,
            'navigation' => true,
            'alerts' => true,
            'badges' => true,
            'progress' => true,
            'tabs' => true,
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
        'feature_access_control' => true,
        'audit_logging' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'caching' => [
            'enabled' => true,
            'driver' => 'redis',
            'ttl' => 3600,
        ],
        'queue_processing' => true,
        'background_jobs' => true,
        'asset_optimization' => true,
        'database_optimization' => true,
    ]
];
