<?php

use LaravelStarterKit\MultiStack\Models\DynamicFeature;
use LaravelStarterKit\MultiStack\Models\FeatureVersion;

beforeEach(function () {
    $this->feature = DynamicFeature::factory()->create();
    $this->version = FeatureVersion::factory()->create(['feature_id' => $this->feature->id]);
});

describe('Dynamic Feature Model', function () {
    it('can be created', function () {
        expect($this->feature)->toBeInstanceOf(DynamicFeature::class);
        expect($this->feature->exists)->toBeTrue();
    });

    it('has fillable attributes', function () {
        $fillable = [
            'name',
            'slug',
            'description',
            'type',
            'category',
            'is_enabled',
            'is_ai_generated',
            'configuration',
            'code_snapshot',
            'metadata',
            'created_by',
            'updated_by',
        ];
        
        foreach ($fillable as $attribute) {
            expect($this->feature->isFillable($attribute))->toBeTrue();
        }
    });

    it('has correct casts', function () {
        expect($this->feature->is_enabled)->toBeBool();
        expect($this->feature->is_ai_generated)->toBeBool();
        expect($this->feature->configuration)->toBeArray();
        expect($this->feature->code_snapshot)->toBeArray();
        expect($this->feature->metadata)->toBeArray();
    });

    it('can get status attribute', function () {
        $this->feature->is_enabled = true;
        expect($this->feature->status)->toBe('enabled');
        
        $this->feature->is_enabled = false;
        expect($this->feature->status)->toBe('disabled');
    });

    it('can get status color attribute', function () {
        $this->feature->is_enabled = true;
        expect($this->feature->status_color)->toBe('green');
        
        $this->feature->is_enabled = false;
        expect($this->feature->status_color)->toBe('red');
    });

    it('can get AI provider attribute', function () {
        $this->feature->metadata = ['ai_provider' => 'openai'];
        expect($this->feature->ai_provider)->toBe('openai');
    });

    it('can get AI prompt attribute', function () {
        $this->feature->metadata = ['ai_prompt' => 'Create a blog system'];
        expect($this->feature->ai_prompt)->toBe('Create a blog system');
    });

    it('can get generated at attribute', function () {
        $this->feature->metadata = ['generated_at' => '2024-01-01T00:00:00Z'];
        expect($this->feature->generated_at)->toBe('2024-01-01T00:00:00Z');
    });

    it('can get version attribute', function () {
        $this->feature->metadata = ['version' => '1.0.0'];
        expect($this->feature->version)->toBe('1.0.0');
    });

    it('can scope enabled features', function () {
        DynamicFeature::factory()->create(['is_enabled' => true]);
        DynamicFeature::factory()->create(['is_enabled' => false]);
        
        $enabledFeatures = DynamicFeature::enabled()->get();
        
        expect($enabledFeatures)->toHaveCount(2); // Including the one from beforeEach
    });

    it('can scope disabled features', function () {
        DynamicFeature::factory()->create(['is_enabled' => false]);
        
        $disabledFeatures = DynamicFeature::disabled()->get();
        
        expect($disabledFeatures)->toHaveCount(1);
    });

    it('can scope AI generated features', function () {
        DynamicFeature::factory()->create(['is_ai_generated' => true]);
        DynamicFeature::factory()->create(['is_ai_generated' => false]);
        
        $aiFeatures = DynamicFeature::aiGenerated()->get();
        
        expect($aiFeatures)->toHaveCount(2); // Including the one from beforeEach
    });

    it('can scope by category', function () {
        DynamicFeature::factory()->create(['category' => 'authentication']);
        DynamicFeature::factory()->create(['category' => 'content_management']);
        
        $authFeatures = DynamicFeature::byCategory('authentication')->get();
        $contentFeatures = DynamicFeature::byCategory('content_management')->get();
        
        expect($authFeatures)->toHaveCount(1);
        expect($contentFeatures)->toHaveCount(1);
    });

    it('can scope by type', function () {
        DynamicFeature::factory()->create(['type' => 'crud']);
        DynamicFeature::factory()->create(['type' => 'api']);
        
        $crudFeatures = DynamicFeature::byType('crud')->get();
        $apiFeatures = DynamicFeature::byType('api')->get();
        
        expect($crudFeatures)->toHaveCount(1);
        expect($apiFeatures)->toHaveCount(1);
    });

    it('can search features', function () {
        DynamicFeature::factory()->create(['name' => 'Blog System']);
        DynamicFeature::factory()->create(['name' => 'User Management']);
        
        $blogFeatures = DynamicFeature::search('blog')->get();
        $userFeatures = DynamicFeature::search('user')->get();
        
        expect($blogFeatures)->toHaveCount(1);
        expect($userFeatures)->toHaveCount(1);
    });

    it('can toggle feature status', function () {
        $this->feature->is_enabled = true;
        $result = $this->feature->toggle();
        
        expect($result)->toBeFalse();
        expect($this->feature->fresh()->is_enabled)->toBeFalse();
    });

    it('can enable feature', function () {
        $this->feature->is_enabled = false;
        $result = $this->feature->enable();
        
        expect($result)->toBeTrue();
        expect($this->feature->fresh()->is_enabled)->toBeTrue();
    });

    it('can disable feature', function () {
        $this->feature->is_enabled = true;
        $result = $this->feature->disable();
        
        expect($result)->toBeTrue();
        expect($this->feature->fresh()->is_enabled)->toBeFalse();
    });

    it('can get route name', function () {
        expect($this->feature->getRouteName())->toBe($this->feature->slug);
    });

    it('can get controller name', function () {
        $expected = \Illuminate\Support\Str::studly($this->feature->slug) . 'Controller';
        expect($this->feature->getControllerName())->toBe($expected);
    });

    it('can get model name from configuration', function () {
        $this->feature->configuration = ['model' => 'TestModel'];
        expect($this->feature->getModelName())->toBe('TestModel');
    });

    it('can get table name from configuration', function () {
        $this->feature->configuration = ['table' => 'test_table'];
        expect($this->feature->getTableName())->toBe('test_table');
    });

    it('can get view path', function () {
        expect($this->feature->getViewPath())->toBe($this->feature->slug);
    });

    it('can get API route prefix', function () {
        $expected = "api/v1/{$this->feature->slug}";
        expect($this->feature->getApiRoutePrefix())->toBe($expected);
    });

    it('can get permissions from configuration', function () {
        $this->feature->configuration = ['permissions' => ['view', 'create']];
        expect($this->feature->getPermissions())->toBe(['view', 'create']);
    });

    it('can get middleware from configuration', function () {
        $this->feature->configuration = ['middleware' => ['auth', 'role:admin']];
        expect($this->feature->getMiddleware())->toBe(['auth', 'role:admin']);
    });

    it('can check if API is enabled', function () {
        $this->feature->configuration = ['api_enabled' => true];
        expect($this->feature->isApiEnabled())->toBeTrue();
        
        $this->feature->configuration = ['api_enabled' => false];
        expect($this->feature->isApiEnabled())->toBeFalse();
    });

    it('can check if dashboard widget exists', function () {
        $this->feature->configuration = ['dashboard_widget' => true];
        expect($this->feature->hasDashboardWidget())->toBeTrue();
        
        $this->feature->configuration = ['dashboard_widget' => false];
        expect($this->feature->hasDashboardWidget())->toBeFalse();
    });

    it('can get fields from configuration', function () {
        $fields = [
            ['name' => 'title', 'type' => 'string'],
            ['name' => 'content', 'type' => 'text']
        ];
        $this->feature->configuration = ['fields' => $fields];
        expect($this->feature->getFields())->toBe($fields);
    });

    it('can get model configuration', function () {
        $this->feature->configuration = [
            'model' => 'TestModel',
            'table' => 'test_table',
            'fields' => [['name' => 'title', 'type' => 'string']]
        ];
        
        $config = $this->feature->getModelConfiguration();
        
        expect($config)->toBeArray()
            ->toHaveKey('model')
            ->toHaveKey('table')
            ->toHaveKey('fields');
        
        expect($config['model'])->toBe('TestModel');
        expect($config['table'])->toBe('test_table');
    });

    it('can get controller configuration', function () {
        $this->feature->configuration = [
            'routes' => [
                'web' => ['index', 'create', 'store'],
                'api' => ['apiIndex', 'apiShow']
            ]
        ];
        
        $config = $this->feature->getControllerConfiguration();
        
        expect($config)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('namespace')
            ->toHaveKey('methods')
            ->toHaveKey('api_methods');
    });

    it('can get view configuration', function () {
        $this->feature->code_snapshot = [
            'views' => [
                'index' => '// Index view',
                'create' => '// Create view'
            ]
        ];
        
        $config = $this->feature->getViewConfiguration();
        
        expect($config)->toBeArray()
            ->toHaveKey('path')
            ->toHaveKey('views')
            ->toHaveKey('layout');
    });

    it('can get route configuration', function () {
        $this->feature->configuration = [
            'middleware' => ['auth', 'role:admin']
        ];
        
        $config = $this->feature->getRouteConfiguration();
        
        expect($config)->toBeArray()
            ->toHaveKey('web')
            ->toHaveKey('api');
        
        expect($config['web'])->toHaveKey('prefix')
            ->toHaveKey('name')
            ->toHaveKey('middleware');
    });

    it('can get full configuration', function () {
        $config = $this->feature->getFullConfiguration();
        
        expect($config)->toBeArray()
            ->toHaveKey('feature')
            ->toHaveKey('model')
            ->toHaveKey('controller')
            ->toHaveKey('views')
            ->toHaveKey('routes');
    });

    it('can get code files', function () {
        $codeFiles = $this->feature->getCodeFiles();
        expect($codeFiles)->toBeArray();
    });

    it('can get model code', function () {
        $this->feature->code_snapshot = ['model' => '// Model code'];
        expect($this->feature->getModelCode())->toBe('// Model code');
    });

    it('can get controller code', function () {
        $this->feature->code_snapshot = ['controller' => '// Controller code'];
        expect($this->feature->getControllerCode())->toBe('// Controller code');
    });

    it('can get views code', function () {
        $views = ['index' => '// Index view', 'create' => '// Create view'];
        $this->feature->code_snapshot = ['views' => $views];
        expect($this->feature->getViewsCode())->toBe($views);
    });

    it('can get migration code', function () {
        $this->feature->code_snapshot = ['migration' => '// Migration code'];
        expect($this->feature->getMigrationCode())->toBe('// Migration code');
    });

    it('can get route code', function () {
        $this->feature->code_snapshot = ['routes' => '// Route code'];
        expect($this->feature->getRouteCode())->toBe('// Route code');
    });

    it('can get API route code', function () {
        $this->feature->code_snapshot = ['api_routes' => '// API route code'];
        expect($this->feature->getApiRouteCode())->toBe('// API route code');
    });

    it('can get full code snapshot', function () {
        $this->feature->code_snapshot = [
            'model' => '// Model code',
            'controller' => '// Controller code',
            'views' => ['index' => '// Index view'],
            'migration' => '// Migration code',
            'routes' => '// Route code',
            'api_routes' => '// API route code'
        ];
        
        $snapshot = $this->feature->getFullCodeSnapshot();
        
        expect($snapshot)->toBeArray()
            ->toHaveKey('model')
            ->toHaveKey('controller')
            ->toHaveKey('views')
            ->toHaveKey('migration')
            ->toHaveKey('routes')
            ->toHaveKey('api_routes');
    });

    it('can get stats', function () {
        $stats = $this->feature->getStats();
        
        expect($stats)->toBeArray()
            ->toHaveKey('total_versions')
            ->toHaveKey('active_version')
            ->toHaveKey('last_updated')
            ->toHaveKey('is_ai_generated')
            ->toHaveKey('ai_provider');
    });
});

describe('Feature Version Model', function () {
    it('can be created', function () {
        expect($this->version)->toBeInstanceOf(FeatureVersion::class);
        expect($this->version->exists)->toBeTrue();
    });

    it('has fillable attributes', function () {
        $fillable = [
            'feature_id',
            'version',
            'description',
            'code_snapshot',
            'is_active',
            'created_by',
        ];
        
        foreach ($fillable as $attribute) {
            expect($this->version->isFillable($attribute))->toBeTrue();
        }
    });

    it('has correct casts', function () {
        expect($this->version->code_snapshot)->toBeArray();
        expect($this->version->is_active)->toBeBool();
    });

    it('can scope active versions', function () {
        FeatureVersion::factory()->create(['is_active' => true]);
        FeatureVersion::factory()->create(['is_active' => false]);
        
        $activeVersions = FeatureVersion::active()->get();
        
        expect($activeVersions)->toHaveCount(2); // Including the one from beforeEach
    });

    it('can scope by version', function () {
        FeatureVersion::factory()->create(['version' => '1.0.0']);
        FeatureVersion::factory()->create(['version' => '1.1.0']);
        
        $version1 = FeatureVersion::byVersion('1.0.0')->get();
        $version2 = FeatureVersion::byVersion('1.1.0')->get();
        
        expect($version1)->toHaveCount(1);
        expect($version2)->toHaveCount(1);
    });

    it('can activate version', function () {
        $version1 = FeatureVersion::factory()->create([
            'feature_id' => $this->feature->id,
            'is_active' => true
        ]);
        
        $version2 = FeatureVersion::factory()->create([
            'feature_id' => $this->feature->id,
            'is_active' => false
        ]);
        
        $version2->activate();
        
        expect($version1->fresh()->is_active)->toBeFalse();
        expect($version2->fresh()->is_active)->toBeTrue();
    });

    it('can deactivate version', function () {
        $this->version->is_active = true;
        $result = $this->version->deactivate();
        
        expect($result)->toBeTrue();
        expect($this->version->fresh()->is_active)->toBeFalse();
    });

    it('can get code files', function () {
        $codeFiles = $this->version->getCodeFiles();
        expect($codeFiles)->toBeArray();
    });

    it('can get model code', function () {
        $this->version->code_snapshot = ['model' => '// Model code'];
        expect($this->version->getModelCode())->toBe('// Model code');
    });

    it('can get controller code', function () {
        $this->version->code_snapshot = ['controller' => '// Controller code'];
        expect($this->version->getControllerCode())->toBe('// Controller code');
    });

    it('can get views code', function () {
        $views = ['index' => '// Index view', 'create' => '// Create view'];
        $this->version->code_snapshot = ['views' => $views];
        expect($this->version->getViewsCode())->toBe($views);
    });

    it('can get migration code', function () {
        $this->version->code_snapshot = ['migration' => '// Migration code'];
        expect($this->version->getMigrationCode())->toBe('// Migration code');
    });

    it('can get route code', function () {
        $this->version->code_snapshot = ['routes' => '// Route code'];
        expect($this->version->getRouteCode())->toBe('// Route code');
    });

    it('can get API route code', function () {
        $this->version->code_snapshot = ['api_routes' => '// API route code'];
        expect($this->version->getApiRouteCode())->toBe('// API route code');
    });

    it('can get full code snapshot', function () {
        $this->version->code_snapshot = [
            'model' => '// Model code',
            'controller' => '// Controller code',
            'views' => ['index' => '// Index view'],
            'migration' => '// Migration code',
            'routes' => '// Route code',
            'api_routes' => '// API route code'
        ];
        
        $snapshot = $this->version->getFullCodeSnapshot();
        
        expect($snapshot)->toBeArray()
            ->toHaveKey('model')
            ->toHaveKey('controller')
            ->toHaveKey('views')
            ->toHaveKey('migration')
            ->toHaveKey('routes')
            ->toHaveKey('api_routes');
    });

    it('can get version info', function () {
        $info = $this->version->getVersionInfo();
        
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
        $this->version->code_snapshot = [
            'model' => '// Model code',
            'controller' => '// Controller code',
            'views' => ['index' => '// Index view'],
            'routes' => '// Route code',
            'api_routes' => '// API route code',
            'migration' => '// Migration code'
        ];
        
        $summary = $this->version->getChangeSummary();
        
        expect($summary)->toBeArray()
            ->toHaveKey('total_files')
            ->toHaveKey('model_changed')
            ->toHaveKey('controller_changed')
            ->toHaveKey('views_changed')
            ->toHaveKey('routes_changed')
            ->toHaveKey('api_routes_changed')
            ->toHaveKey('migration_changed');
    });
});
