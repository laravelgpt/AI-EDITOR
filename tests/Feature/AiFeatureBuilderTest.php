<?php

use AiEditor\AiTextEditor\Services\AiFeatureBuilderService;
use AiEditor\AiTextEditor\Models\DynamicFeature;
use AiEditor\AiTextEditor\Models\FeatureVersion;

beforeEach(function () {
    $this->aiService = app(AiFeatureBuilderService::class);
});

describe('AI Feature Builder Service', function () {
    it('can get available providers', function () {
        $providers = $this->aiService->getAvailableProviders();
        
        expect($providers)->toBeArray()
            ->toContain('openai')
            ->toContain('anthropic')
            ->toContain('google');
    });

    it('can get provider info', function () {
        $info = $this->aiService->getProviderInfo('openai');
        
        expect($info)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('configured')
            ->toHaveKey('model');
    });

    it('throws exception for invalid provider', function () {
        expect(fn() => $this->aiService->getProviderInfo('invalid-provider'))
            ->toThrow(Exception::class);
    });

    it('can generate feature with mock response', function () {
        // Mock the AI service response
        $mockResponse = [
            'success' => true,
            'content' => json_encode([
                'name' => 'Test Feature',
                'description' => 'A test feature',
                'type' => 'crud',
                'category' => 'custom',
                'configuration' => [
                    'model' => 'TestModel',
                    'table' => 'test_table',
                    'fields' => [
                        ['name' => 'name', 'type' => 'string', 'nullable' => false],
                        ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ],
                    'permissions' => ['view', 'create', 'edit', 'delete'],
                    'middleware' => ['auth'],
                    'api_enabled' => true,
                    'dashboard_widget' => false,
                    'routes' => [
                        'web' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'],
                        'api' => ['index', 'show', 'store', 'update', 'destroy']
                    ]
                ],
                'code' => [
                    'model' => '// Model code',
                    'controller' => '// Controller code',
                    'migration' => '// Migration code',
                    'views' => [
                        'index' => '// Index view',
                        'create' => '// Create view',
                        'edit' => '// Edit view',
                        'show' => '// Show view'
                    ],
                    'routes' => '// Route definitions',
                    'api_routes' => '// API route definitions'
                ]
            ]),
            'model' => 'gpt-4',
            'usage' => null
        ];

        // Mock the HTTP call
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => $mockResponse['content']
                        ]
                    ]
                ],
                'model' => 'gpt-4',
                'usage' => null
            ], 200)
        ]);

        $result = $this->aiService->generateFeature(
            'Create a blog system with categories and tags',
            'content_management',
            'openai'
        );

        expect($result)->toHaveKey('success');
        
        if ($result['success']) {
            expect($result)->toHaveKey('feature')
                ->toHaveKey('generated_code')
                ->toHaveKey('provider');
            
            expect($result['feature'])->toBeInstanceOf(DynamicFeature::class);
            expect($result['provider'])->toBe('openai');
        }
    });

    it('handles AI generation failure gracefully', function () {
        // Mock failed HTTP response
        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'API key invalid'], 401)
        ]);

        $result = $this->aiService->generateFeature(
            'Create a blog system',
            'custom',
            'openai'
        );

        expect($result)->toHaveKey('success');
        expect($result['success'])->toBeFalse();
        expect($result)->toHaveKey('error');
    });
});

describe('Dynamic Feature Model', function () {
    it('can create dynamic feature', function () {
        $feature = DynamicFeature::create([
            'name' => 'Test Feature',
            'slug' => 'test-feature',
            'description' => 'A test feature',
            'type' => 'crud',
            'category' => 'custom',
            'is_enabled' => true,
            'is_ai_generated' => true,
            'configuration' => [
                'model' => 'TestModel',
                'table' => 'test_table',
                'fields' => [
                    ['name' => 'name', 'type' => 'string', 'nullable' => false],
                ],
                'permissions' => ['view', 'create'],
                'middleware' => ['auth'],
                'api_enabled' => true,
            ],
            'code_snapshot' => [
                'model' => '// Model code',
                'controller' => '// Controller code',
            ],
            'metadata' => [
                'ai_provider' => 'openai',
                'ai_prompt' => 'Create a test feature',
                'generated_at' => now()->toISOString(),
                'version' => '1.0.0',
            ],
        ]);

        expect($feature)->toBeInstanceOf(DynamicFeature::class);
        expect($feature->name)->toBe('Test Feature');
        expect($feature->slug)->toBe('test-feature');
        expect($feature->is_ai_generated)->toBeTrue();
        expect($feature->ai_provider)->toBe('openai');
    });

    it('can toggle feature status', function () {
        $feature = DynamicFeature::factory()->create(['is_enabled' => true]);
        
        $result = $feature->toggle();
        
        expect($result)->toBeFalse();
        expect($feature->fresh()->is_enabled)->toBeFalse();
    });

    it('can get feature configuration', function () {
        $feature = DynamicFeature::factory()->create([
            'configuration' => [
                'model' => 'TestModel',
                'table' => 'test_table',
                'permissions' => ['view', 'create'],
                'middleware' => ['auth'],
                'api_enabled' => true,
            ]
        ]);

        expect($feature->getModelName())->toBe('TestModel');
        expect($feature->getTableName())->toBe('test_table');
        expect($feature->getPermissions())->toBe(['view', 'create']);
        expect($feature->getMiddleware())->toBe(['auth']);
        expect($feature->isApiEnabled())->toBeTrue();
    });

    it('can get feature stats', function () {
        $feature = DynamicFeature::factory()->create();
        
        $stats = $feature->getStats();
        
        expect($stats)->toBeArray()
            ->toHaveKey('total_versions')
            ->toHaveKey('active_version')
            ->toHaveKey('last_updated')
            ->toHaveKey('is_ai_generated')
            ->toHaveKey('ai_provider');
    });

    it('can scope features by category', function () {
        DynamicFeature::factory()->create(['category' => 'authentication']);
        DynamicFeature::factory()->create(['category' => 'content_management']);
        
        $authFeatures = DynamicFeature::byCategory('authentication')->get();
        $contentFeatures = DynamicFeature::byCategory('content_management')->get();
        
        expect($authFeatures)->toHaveCount(1);
        expect($contentFeatures)->toHaveCount(1);
    });

    it('can scope AI generated features', function () {
        DynamicFeature::factory()->create(['is_ai_generated' => true]);
        DynamicFeature::factory()->create(['is_ai_generated' => false]);
        
        $aiFeatures = DynamicFeature::aiGenerated()->get();
        
        expect($aiFeatures)->toHaveCount(1);
        expect($aiFeatures->first()->is_ai_generated)->toBeTrue();
    });

    it('can search features', function () {
        DynamicFeature::factory()->create(['name' => 'Blog System']);
        DynamicFeature::factory()->create(['name' => 'User Management']);
        
        $blogFeatures = DynamicFeature::search('blog')->get();
        $userFeatures = DynamicFeature::search('user')->get();
        
        expect($blogFeatures)->toHaveCount(1);
        expect($userFeatures)->toHaveCount(1);
    });
});

describe('Feature Version Model', function () {
    it('can create feature version', function () {
        $feature = DynamicFeature::factory()->create();
        
        $version = FeatureVersion::create([
            'feature_id' => $feature->id,
            'version' => '1.0.0',
            'description' => 'Initial version',
            'code_snapshot' => [
                'model' => '// Model code',
                'controller' => '// Controller code',
            ],
            'is_active' => true,
        ]);

        expect($version)->toBeInstanceOf(FeatureVersion::class);
        expect($version->feature_id)->toBe($feature->id);
        expect($version->version)->toBe('1.0.0');
        expect($version->is_active)->toBeTrue();
    });

    it('can activate version', function () {
        $feature = DynamicFeature::factory()->create();
        
        $version1 = FeatureVersion::factory()->create([
            'feature_id' => $feature->id,
            'version' => '1.0.0',
            'is_active' => true
        ]);
        
        $version2 = FeatureVersion::factory()->create([
            'feature_id' => $feature->id,
            'version' => '1.1.0',
            'is_active' => false
        ]);
        
        $version2->activate();
        
        expect($version1->fresh()->is_active)->toBeFalse();
        expect($version2->fresh()->is_active)->toBeTrue();
    });

    it('can get version info', function () {
        $feature = DynamicFeature::factory()->create();
        $version = FeatureVersion::factory()->create([
            'feature_id' => $feature->id,
            'version' => '1.0.0',
            'description' => 'Initial version',
        ]);
        
        $info = $version->getVersionInfo();
        
        expect($info)->toBeArray()
            ->toHaveKey('id')
            ->toHaveKey('version')
            ->toHaveKey('description')
            ->toHaveKey('is_active')
            ->toHaveKey('created_at')
            ->toHaveKey('feature_name')
            ->toHaveKey('code_files_count');
    });

    it('can get change summary', function () {
        $feature = DynamicFeature::factory()->create();
        $version = FeatureVersion::factory()->create([
            'feature_id' => $feature->id,
            'code_snapshot' => [
                'model' => '// Model code',
                'controller' => '// Controller code',
                'views' => ['index' => '// Index view'],
                'routes' => '// Route code',
            ]
        ]);
        
        $summary = $version->getChangeSummary();
        
        expect($summary)->toBeArray()
            ->toHaveKey('total_files')
            ->toHaveKey('model_changed')
            ->toHaveKey('controller_changed')
            ->toHaveKey('views_changed')
            ->toHaveKey('routes_changed');
    });
});
