<?php

namespace Tests\Unit;

use Tests\TestCase;
use AiEditor\AiTextEditor\Models\DynamicFeature;
use AiEditor\AiTextEditor\Models\FeatureVersion;

class FactoryTest extends TestCase
{
    public function test_can_create_feature_with_default_attributes()
    {
        $feature = DynamicFeature::factory()->create();
        
        $this->assertInstanceOf(DynamicFeature::class, $feature);
        $this->assertIsString($feature->name);
        $this->assertIsString($feature->slug);
        $this->assertIsString($feature->description);
        $this->assertIsString($feature->type);
        $this->assertIsString($feature->category);
        $this->assertIsBool($feature->is_enabled);
        $this->assertIsBool($feature->is_ai_generated);
        $this->assertIsArray($feature->configuration);
        $this->assertIsArray($feature->code_snapshot);
        $this->assertIsArray($feature->metadata);
    }

    public function test_can_create_feature_with_custom_attributes()
    {
        $feature = DynamicFeature::factory()->create([
            'name' => 'Custom Feature',
            'slug' => 'custom-feature',
            'description' => 'A custom feature',
            'type' => 'crud',
            'category' => 'custom',
            'is_enabled' => true,
            'is_ai_generated' => false,
        ]);
        
        $this->assertEquals('Custom Feature', $feature->name);
        $this->assertEquals('custom-feature', $feature->slug);
        $this->assertEquals('A custom feature', $feature->description);
        $this->assertEquals('crud', $feature->type);
        $this->assertEquals('custom', $feature->category);
        $this->assertTrue($feature->is_enabled);
        $this->assertFalse($feature->is_ai_generated);
    }

    public function test_can_create_ai_generated_feature()
    {
        $feature = DynamicFeature::factory()->aiGenerated()->create();
        
        $this->assertTrue($feature->is_ai_generated);
        $this->assertArrayHasKey('ai_provider', $feature->metadata);
        $this->assertArrayHasKey('ai_prompt', $feature->metadata);
        $this->assertArrayHasKey('generated_at', $feature->metadata);
        $this->assertArrayHasKey('version', $feature->metadata);
    }

    public function test_can_create_enabled_feature()
    {
        $feature = DynamicFeature::factory()->enabled()->create();
        
        $this->assertTrue($feature->is_enabled);
    }

    public function test_can_create_disabled_feature()
    {
        $feature = DynamicFeature::factory()->disabled()->create();
        
        $this->assertFalse($feature->is_enabled);
    }

    public function test_can_create_feature_with_specific_category()
    {
        $feature = DynamicFeature::factory()->create(['category' => 'authentication']);
        
        $this->assertEquals('authentication', $feature->category);
    }

    public function test_can_create_feature_with_specific_type()
    {
        $feature = DynamicFeature::factory()->create(['type' => 'api']);
        
        $this->assertEquals('api', $feature->type);
    }

    public function test_can_create_multiple_features()
    {
        $features = DynamicFeature::factory()->count(3)->create();
        
        $this->assertCount(3, $features);
        $this->assertInstanceOf(DynamicFeature::class, $features->first());
    }

    public function test_can_create_feature_with_custom_configuration()
    {
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
        
        $this->assertEquals($configuration, $feature->configuration);
        $this->assertEquals('TestModel', $feature->getModelName());
        $this->assertEquals('test_table', $feature->getTableName());
        $this->assertEquals(['view', 'create', 'edit', 'delete'], $feature->getPermissions());
        $this->assertEquals(['auth'], $feature->getMiddleware());
        $this->assertTrue($feature->isApiEnabled());
        $this->assertFalse($feature->hasDashboardWidget());
    }

    public function test_can_create_feature_with_custom_code_snapshot()
    {
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
        
        $this->assertEquals($codeSnapshot, $feature->code_snapshot);
        $this->assertEquals('// Model code', $feature->getModelCode());
        $this->assertEquals('// Controller code', $feature->getControllerCode());
        $this->assertEquals('// Migration code', $feature->getMigrationCode());
        $this->assertEquals($codeSnapshot['views'], $feature->getViewsCode());
        $this->assertEquals('// Route definitions', $feature->getRouteCode());
        $this->assertEquals('// API route definitions', $feature->getApiRouteCode());
    }

    public function test_can_create_feature_with_custom_metadata()
    {
        $metadata = [
            'ai_provider' => 'openai',
            'ai_prompt' => 'Create a blog system with categories and tags',
            'generated_at' => '2024-01-01T00:00:00Z',
            'version' => '1.0.0',
            'custom_field' => 'custom_value'
        ];
        
        $feature = DynamicFeature::factory()->create(['metadata' => $metadata]);
        
        $this->assertEquals($metadata, $feature->metadata);
        $this->assertEquals('openai', $feature->ai_provider);
        $this->assertEquals('Create a blog system with categories and tags', $feature->ai_prompt);
        $this->assertEquals('2024-01-01T00:00:00Z', $feature->generated_at);
        $this->assertEquals('1.0.0', $feature->version);
    }

    public function test_can_create_version_with_default_attributes()
    {
        $version = FeatureVersion::factory()->create();
        
        $this->assertInstanceOf(FeatureVersion::class, $version);
        $this->assertIsInt($version->feature_id);
        $this->assertIsString($version->version);
        $this->assertIsString($version->description);
        $this->assertIsArray($version->code_snapshot);
        $this->assertIsBool($version->is_active);
    }

    public function test_can_create_version_with_custom_attributes()
    {
        $version = FeatureVersion::factory()->create([
            'version' => '2.0.0',
            'description' => 'Major update',
            'is_active' => true,
        ]);
        
        $this->assertEquals('2.0.0', $version->version);
        $this->assertEquals('Major update', $version->description);
        $this->assertTrue($version->is_active);
    }

    public function test_can_create_active_version()
    {
        $version = FeatureVersion::factory()->active()->create();
        
        $this->assertTrue($version->is_active);
    }

    public function test_can_create_inactive_version()
    {
        $version = FeatureVersion::factory()->inactive()->create();
        
        $this->assertFalse($version->is_active);
    }

    public function test_can_create_version_with_custom_code_snapshot()
    {
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
        
        $this->assertEquals($codeSnapshot, $version->code_snapshot);
        $this->assertEquals('// Updated model code', $version->getModelCode());
        $this->assertEquals('// Updated controller code', $version->getControllerCode());
        $this->assertEquals('// Updated migration code', $version->getMigrationCode());
        $this->assertEquals($codeSnapshot['views'], $version->getViewsCode());
        $this->assertEquals('// Updated route definitions', $version->getRouteCode());
        $this->assertEquals('// Updated API route definitions', $version->getApiRouteCode());
    }

    public function test_can_create_multiple_versions()
    {
        $versions = FeatureVersion::factory()->count(3)->create();
        
        $this->assertCount(3, $versions);
        $this->assertInstanceOf(FeatureVersion::class, $versions->first());
    }

    public function test_can_create_version_for_specific_feature()
    {
        $feature = DynamicFeature::factory()->create();
        $version = FeatureVersion::factory()->create(['feature_id' => $feature->id]);
        
        $this->assertEquals($feature->id, $version->feature_id);
        $this->assertInstanceOf(DynamicFeature::class, $version->feature);
        $this->assertEquals($feature->id, $version->feature->id);
    }

    public function test_can_create_feature_with_versions()
    {
        $feature = DynamicFeature::factory()
            ->has(FeatureVersion::factory()->count(3))
            ->create();
        
        $this->assertCount(3, $feature->versions);
        $this->assertInstanceOf(FeatureVersion::class, $feature->versions->first());
    }

    public function test_can_create_version_with_feature()
    {
        $version = FeatureVersion::factory()
            ->for(DynamicFeature::factory())
            ->create();
        
        $this->assertInstanceOf(DynamicFeature::class, $version->feature);
        $this->assertEquals($version->feature->id, $version->feature_id);
    }

    public function test_can_create_feature_with_active_version()
    {
        $feature = DynamicFeature::factory()
            ->has(FeatureVersion::factory()->active())
            ->create();
        
        $this->assertCount(1, $feature->currentVersion);
        $this->assertTrue($feature->currentVersion->first()->is_active);
    }

    public function test_can_create_feature_with_multiple_versions()
    {
        $feature = DynamicFeature::factory()
            ->has(FeatureVersion::factory()->count(2)->sequence(
                ['version' => '1.0.0', 'is_active' => false],
                ['version' => '1.1.0', 'is_active' => true]
            ))
            ->create();
        
        $this->assertCount(2, $feature->versions);
        $this->assertCount(1, $feature->currentVersion);
        $this->assertEquals('1.1.0', $feature->currentVersion->first()->version);
    }
}