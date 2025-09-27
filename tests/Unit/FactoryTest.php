<?php

use LaravelStarterKit\MultiStack\Models\DynamicFeature;
use LaravelStarterKit\MultiStack\Models\FeatureVersion;

beforeEach(function () {
    $this->feature = DynamicFeature::factory()->create();
    $this->version = FeatureVersion::factory()->create(['feature_id' => $this->feature->id]);
});

describe('Dynamic Feature Factory', function () {
    it('can create feature with default attributes', function () {
        $feature = DynamicFeature::factory()->create();
        
        expect($feature)->toBeInstanceOf(DynamicFeature::class);
        expect($feature->name)->toBeString();
        expect($feature->slug)->toBeString();
        expect($feature->description)->toBeString();
        expect($feature->type)->toBeString();
        expect($feature->category)->toBeString();
        expect($feature->is_enabled)->toBeBool();
        expect($feature->is_ai_generated)->toBeBool();
        expect($feature->configuration)->toBeArray();
        expect($feature->code_snapshot)->toBeArray();
        expect($feature->metadata)->toBeArray();
    });

    it('can create feature with custom attributes', function () {
        $feature = DynamicFeature::factory()->create([
            'name' => 'Custom Feature',
            'slug' => 'custom-feature',
            'description' => 'A custom feature',
            'type' => 'crud',
            'category' => 'custom',
            'is_enabled' => true,
            'is_ai_generated' => false,
        ]);
        
        expect($feature->name)->toBe('Custom Feature');
        expect($feature->slug)->toBe('custom-feature');
        expect($feature->description)->toBe('A custom feature');
        expect($feature->type)->toBe('crud');
        expect($feature->category)->toBe('custom');
        expect($feature->is_enabled)->toBeTrue();
        expect($feature->is_ai_generated)->toBeFalse();
    });

    it('can create AI generated feature', function () {
        $feature = DynamicFeature::factory()->aiGenerated()->create();
        
        expect($feature->is_ai_generated)->toBeTrue();
        expect($feature->metadata)->toHaveKey('ai_provider');
        expect($feature->metadata)->toHaveKey('ai_prompt');
        expect($feature->metadata)->toHaveKey('generated_at');
        expect($feature->metadata)->toHaveKey('version');
    });

    it('can create enabled feature', function () {
        $feature = DynamicFeature::factory()->enabled()->create();
        
        expect($feature->is_enabled)->toBeTrue();
    });

    it('can create disabled feature', function () {
        $feature = DynamicFeature::factory()->disabled()->create();
        
        expect($feature->is_enabled)->toBeFalse();
    });

    it('can create feature with specific category', function () {
        $feature = DynamicFeature::factory()->create(['category' => 'authentication']);
        
        expect($feature->category)->toBe('authentication');
    });

    it('can create feature with specific type', function () {
        $feature = DynamicFeature::factory()->create(['type' => 'api']);
        
        expect($feature->type)->toBe('api');
    });

    it('can create multiple features', function () {
        $features = DynamicFeature::factory()->count(3)->create();
        
        expect($features)->toHaveCount(3);
        expect($features->first())->toBeInstanceOf(DynamicFeature::class);
    });

    it('can create feature with custom configuration', function () {
        $configuration = [
            'model' => 'TestModel',
            'table' => 'test_table',
            'fields' => [
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'content', 'type' => 'text']
            ],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'middleware' => ['auth'],
            'api_enabled' => true,
            'dashboard_widget' => false,
            'routes' => [
                'web' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'],
                'api' => ['index', 'show', 'store', 'update', 'destroy']
            ]
        ];
        
        $feature = DynamicFeature::factory()->create(['configuration' => $configuration]);
        
        expect($feature->configuration)->toBe($configuration);
        expect($feature->getModelName())->toBe('TestModel');
        expect($feature->getTableName())->toBe('test_table');
        expect($feature->getPermissions())->toBe(['view', 'create', 'edit', 'delete']);
        expect($feature->getMiddleware())->toBe(['auth']);
        expect($feature->isApiEnabled())->toBeTrue();
        expect($feature->hasDashboardWidget())->toBeFalse();
    });

    it('can create feature with custom code snapshot', function () {
        $codeSnapshot = [
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
        ];
        
        $feature = DynamicFeature::factory()->create(['code_snapshot' => $codeSnapshot]);
        
        expect($feature->code_snapshot)->toBe($codeSnapshot);
        expect($feature->getModelCode())->toBe('// Model code');
        expect($feature->getControllerCode())->toBe('// Controller code');
        expect($feature->getMigrationCode())->toBe('// Migration code');
        expect($feature->getViewsCode())->toBe($codeSnapshot['views']);
        expect($feature->getRouteCode())->toBe('// Route definitions');
        expect($feature->getApiRouteCode())->toBe('// API route definitions');
    });

    it('can create feature with custom metadata', function () {
        $metadata = [
            'ai_provider' => 'openai',
            'ai_prompt' => 'Create a blog system with categories and tags',
            'generated_at' => '2024-01-01T00:00:00Z',
            'version' => '1.0.0',
            'custom_field' => 'custom_value'
        ];
        
        $feature = DynamicFeature::factory()->create(['metadata' => $metadata]);
        
        expect($feature->metadata)->toBe($metadata);
        expect($feature->ai_provider)->toBe('openai');
        expect($feature->ai_prompt)->toBe('Create a blog system with categories and tags');
        expect($feature->generated_at)->toBe('2024-01-01T00:00:00Z');
        expect($feature->version)->toBe('1.0.0');
    });
});

describe('Feature Version Factory', function () {
    it('can create version with default attributes', function () {
        $version = FeatureVersion::factory()->create();
        
        expect($version)->toBeInstanceOf(FeatureVersion::class);
        expect($version->feature_id)->toBeInt();
        expect($version->version)->toBeString();
        expect($version->description)->toBeString();
        expect($version->code_snapshot)->toBeArray();
        expect($version->is_active)->toBeBool();
    });

    it('can create version with custom attributes', function () {
        $version = FeatureVersion::factory()->create([
            'version' => '2.0.0',
            'description' => 'Major update',
            'is_active' => true,
        ]);
        
        expect($version->version)->toBe('2.0.0');
        expect($version->description)->toBe('Major update');
        expect($version->is_active)->toBeTrue();
    });

    it('can create active version', function () {
        $version = FeatureVersion::factory()->active()->create();
        
        expect($version->is_active)->toBeTrue();
    });

    it('can create inactive version', function () {
        $version = FeatureVersion::factory()->inactive()->create();
        
        expect($version->is_active)->toBeFalse();
    });

    it('can create version with custom code snapshot', function () {
        $codeSnapshot = [
            'model' => '// Updated model code',
            'controller' => '// Updated controller code',
            'migration' => '// Updated migration code',
            'views' => [
                'index' => '// Updated index view',
                'create' => '// Updated create view'
            ],
            'routes' => '// Updated route definitions',
            'api_routes' => '// Updated API route definitions'
        ];
        
        $version = FeatureVersion::factory()->create(['code_snapshot' => $codeSnapshot]);
        
        expect($version->code_snapshot)->toBe($codeSnapshot);
        expect($version->getModelCode())->toBe('// Updated model code');
        expect($version->getControllerCode())->toBe('// Updated controller code');
        expect($version->getMigrationCode())->toBe('// Updated migration code');
        expect($version->getViewsCode())->toBe($codeSnapshot['views']);
        expect($version->getRouteCode())->toBe('// Updated route definitions');
        expect($version->getApiRouteCode())->toBe('// Updated API route definitions');
    });

    it('can create multiple versions', function () {
        $versions = FeatureVersion::factory()->count(3)->create();
        
        expect($versions)->toHaveCount(3);
        expect($versions->first())->toBeInstanceOf(FeatureVersion::class);
    });

    it('can create version for specific feature', function () {
        $feature = DynamicFeature::factory()->create();
        $version = FeatureVersion::factory()->create(['feature_id' => $feature->id]);
        
        expect($version->feature_id)->toBe($feature->id);
        expect($version->feature)->toBeInstanceOf(DynamicFeature::class);
        expect($version->feature->id)->toBe($feature->id);
    });
});

describe('Factory Relationships', function () {
    it('can create feature with versions', function () {
        $feature = DynamicFeature::factory()
            ->has(FeatureVersion::factory()->count(3))
            ->create();
        
        expect($feature->versions)->toHaveCount(3);
        expect($feature->versions->first())->toBeInstanceOf(FeatureVersion::class);
    });

    it('can create version with feature', function () {
        $version = FeatureVersion::factory()
            ->for(DynamicFeature::factory())
            ->create();
        
        expect($version->feature)->toBeInstanceOf(DynamicFeature::class);
        expect($version->feature_id)->toBe($version->feature->id);
    });

    it('can create feature with active version', function () {
        $feature = DynamicFeature::factory()
            ->has(FeatureVersion::factory()->active())
            ->create();
        
        expect($feature->currentVersion)->toHaveCount(1);
        expect($feature->currentVersion->first()->is_active)->toBeTrue();
    });

    it('can create feature with multiple versions', function () {
        $feature = DynamicFeature::factory()
            ->has(FeatureVersion::factory()->count(2)->sequence(
                ['version' => '1.0.0', 'is_active' => false],
                ['version' => '1.1.0', 'is_active' => true]
            ))
            ->create();
        
        expect($feature->versions)->toHaveCount(2);
        expect($feature->currentVersion)->toHaveCount(1);
        expect($feature->currentVersion->first()->version)->toBe('1.1.0');
    });
});
