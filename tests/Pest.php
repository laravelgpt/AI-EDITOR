<?php

use AiEditor\AiTextEditor\AiTextEditorServiceProvider;
use AiEditor\AiTextEditor\Models\DynamicFeature;
use AiEditor\AiTextEditor\Models\FeatureVersion;
use AiEditor\AiTextEditor\Database\Factories\DynamicFeatureFactory;
use AiEditor\AiTextEditor\Database\Factories\FeatureVersionFactory;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce test code duplication.
|
*/

/**
 * Set up the test environment
 */
function setUpTestEnvironment(): void
{
    // Register the service provider
    app()->register(MultiStackServiceProvider::class);
    
    // Run migrations
    \Illuminate\Support\Facades\Artisan::call('migrate');
}

/**
 * Create a test feature
 */
function createTestFeature(array $attributes = []): DynamicFeature
{
    return DynamicFeature::factory()->create($attributes);
}

/**
 * Create a test feature version
 */
function createTestFeatureVersion(array $attributes = []): FeatureVersion
{
    return FeatureVersion::factory()->create($attributes);
}

/**
 * Create an AI generated feature
 */
function createAiGeneratedFeature(array $attributes = []): DynamicFeature
{
    return DynamicFeature::factory()->aiGenerated()->create($attributes);
}

/**
 * Create an enabled feature
 */
function createEnabledFeature(array $attributes = []): DynamicFeature
{
    return DynamicFeature::factory()->enabled()->create($attributes);
}

/**
 * Create a disabled feature
 */
function createDisabledFeature(array $attributes = []): DynamicFeature
{
    return DynamicFeature::factory()->disabled()->create($attributes);
}

/**
 * Create a CRUD feature
 */
function createCrudFeature(array $attributes = []): DynamicFeature
{
    return DynamicFeature::factory()->crud()->create($attributes);
}

/**
 * Create an API feature
 */
function createApiFeature(array $attributes = []): DynamicFeature
{
    return DynamicFeature::factory()->api()->create($attributes);
}

/**
 * Create a dashboard feature
 */
function createDashboardFeature(array $attributes = []): DynamicFeature
{
    return DynamicFeature::factory()->dashboard()->create($attributes);
}

/**
 * Create an active feature version
 */
function createActiveFeatureVersion(array $attributes = []): FeatureVersion
{
    return FeatureVersion::factory()->active()->create($attributes);
}

/**
 * Create an inactive feature version
 */
function createInactiveFeatureVersion(array $attributes = []): FeatureVersion
{
    return FeatureVersion::factory()->inactive()->create($attributes);
}

/**
 * Create a major version
 */
function createMajorVersion(array $attributes = []): FeatureVersion
{
    return FeatureVersion::factory()->major()->create($attributes);
}

/**
 * Create a minor version
 */
function createMinorVersion(array $attributes = []): FeatureVersion
{
    return FeatureVersion::factory()->minor()->create($attributes);
}

/**
 * Create a patch version
 */
function createPatchVersion(array $attributes = []): FeatureVersion
{
    return FeatureVersion::factory()->patch()->create($attributes);
}

/**
 * Assert that a feature has the expected attributes
 */
function assertFeatureHasAttributes(DynamicFeature $feature, array $expectedAttributes): void
{
    foreach ($expectedAttributes as $attribute => $expectedValue) {
        expect($feature->$attribute)->toBe($expectedValue);
    }
}

/**
 * Assert that a feature version has the expected attributes
 */
function assertFeatureVersionHasAttributes(FeatureVersion $version, array $expectedAttributes): void
{
    foreach ($expectedAttributes as $attribute => $expectedValue) {
        expect($version->$attribute)->toBe($expectedValue);
    }
}

/**
 * Assert that a feature is enabled
 */
function assertFeatureIsEnabled(DynamicFeature $feature): void
{
    expect($feature->is_enabled)->toBeTrue();
    expect($feature->status)->toBe('enabled');
    expect($feature->status_color)->toBe('green');
}

/**
 * Assert that a feature is disabled
 */
function assertFeatureIsDisabled(DynamicFeature $feature): void
{
    expect($feature->is_enabled)->toBeFalse();
    expect($feature->status)->toBe('disabled');
    expect($feature->status_color)->toBe('red');
}

/**
 * Assert that a feature is AI generated
 */
function assertFeatureIsAiGenerated(DynamicFeature $feature): void
{
    expect($feature->is_ai_generated)->toBeTrue();
    expect($feature->ai_provider)->not->toBeNull();
    expect($feature->ai_prompt)->not->toBeNull();
    expect($feature->generated_at)->not->toBeNull();
    expect($feature->version)->not->toBeNull();
}

/**
 * Assert that a feature is not AI generated
 */
function assertFeatureIsNotAiGenerated(DynamicFeature $feature): void
{
    expect($feature->is_ai_generated)->toBeFalse();
}

/**
 * Assert that a feature version is active
 */
function assertFeatureVersionIsActive(FeatureVersion $version): void
{
    expect($version->is_active)->toBeTrue();
}

/**
 * Assert that a feature version is inactive
 */
function assertFeatureVersionIsInactive(FeatureVersion $version): void
{
    expect($version->is_active)->toBeFalse();
}

/**
 * Assert that a feature has the expected configuration
 */
function assertFeatureHasConfiguration(DynamicFeature $feature, array $expectedConfiguration): void
{
    foreach ($expectedConfiguration as $key => $expectedValue) {
        expect($feature->configuration[$key])->toBe($expectedValue);
    }
}

/**
 * Assert that a feature has the expected code snapshot
 */
function assertFeatureHasCodeSnapshot(DynamicFeature $feature, array $expectedCodeSnapshot): void
{
    foreach ($expectedCodeSnapshot as $key => $expectedValue) {
        expect($feature->code_snapshot[$key])->toBe($expectedValue);
    }
}

/**
 * Assert that a feature version has the expected code snapshot
 */
function assertFeatureVersionHasCodeSnapshot(FeatureVersion $version, array $expectedCodeSnapshot): void
{
    foreach ($expectedCodeSnapshot as $key => $expectedValue) {
        expect($version->code_snapshot[$key])->toBe($expectedValue);
    }
}

/**
 * Assert that a feature has the expected metadata
 */
function assertFeatureHasMetadata(DynamicFeature $feature, array $expectedMetadata): void
{
    foreach ($expectedMetadata as $key => $expectedValue) {
        expect($feature->metadata[$key])->toBe($expectedValue);
    }
}

/**
 * Assert that a feature can be toggled
 */
function assertFeatureCanBeToggled(DynamicFeature $feature): void
{
    $originalStatus = $feature->is_enabled;
    $newStatus = $feature->toggle();
    
    expect($newStatus)->toBe(!$originalStatus);
    expect($feature->fresh()->is_enabled)->toBe($newStatus);
}

/**
 * Assert that a feature can be enabled
 */
function assertFeatureCanBeEnabled(DynamicFeature $feature): void
{
    $feature->is_enabled = false;
    $feature->save();
    
    $result = $feature->enable();
    
    expect($result)->toBeTrue();
    expect($feature->fresh()->is_enabled)->toBeTrue();
}

/**
 * Assert that a feature can be disabled
 */
function assertFeatureCanBeDisabled(DynamicFeature $feature): void
{
    $feature->is_enabled = true;
    $feature->save();
    
    $result = $feature->disable();
    
    expect($result)->toBeTrue();
    expect($feature->fresh()->is_enabled)->toBeFalse();
}

/**
 * Assert that a feature version can be activated
 */
function assertFeatureVersionCanBeActivated(FeatureVersion $version): void
{
    $version->is_active = false;
    $version->save();
    
    $result = $version->activate();
    
    expect($result)->toBeTrue();
    expect($version->fresh()->is_active)->toBeTrue();
}

/**
 * Assert that a feature version can be deactivated
 */
function assertFeatureVersionCanBeDeactivated(FeatureVersion $version): void
{
    $version->is_active = true;
    $version->save();
    
    $result = $version->deactivate();
    
    expect($result)->toBeTrue();
    expect($version->fresh()->is_active)->toBeFalse();
}

/**
 * Assert that a feature has the expected relationships
 */
function assertFeatureHasRelationships(DynamicFeature $feature): void
{
    expect($feature->versions)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    expect($feature->currentVersion)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
}

/**
 * Assert that a feature version has the expected relationships
 */
function assertFeatureVersionHasRelationships(FeatureVersion $version): void
{
    expect($version->feature)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
}

/**
 * Assert that a feature has the expected scopes
 */
function assertFeatureHasScopes(): void
{
    // Test enabled scope
    DynamicFeature::factory()->enabled()->create();
    DynamicFeature::factory()->disabled()->create();
    
    $enabledFeatures = DynamicFeature::enabled()->get();
    $disabledFeatures = DynamicFeature::disabled()->get();
    
    expect($enabledFeatures->count())->toBeGreaterThan(0);
    expect($disabledFeatures->count())->toBeGreaterThan(0);
    
    // Test AI generated scope
    DynamicFeature::factory()->aiGenerated()->create();
    DynamicFeature::factory()->create(['is_ai_generated' => false]);
    
    $aiFeatures = DynamicFeature::aiGenerated()->get();
    
    expect($aiFeatures->count())->toBeGreaterThan(0);
    
    // Test category scope
    DynamicFeature::factory()->create(['category' => 'authentication']);
    DynamicFeature::factory()->create(['category' => 'content_management']);
    
    $authFeatures = DynamicFeature::byCategory('authentication')->get();
    $contentFeatures = DynamicFeature::byCategory('content_management')->get();
    
    expect($authFeatures->count())->toBeGreaterThan(0);
    expect($contentFeatures->count())->toBeGreaterThan(0);
    
    // Test type scope
    DynamicFeature::factory()->create(['type' => 'crud']);
    DynamicFeature::factory()->create(['type' => 'api']);
    
    $crudFeatures = DynamicFeature::byType('crud')->get();
    $apiFeatures = DynamicFeature::byType('api')->get();
    
    expect($crudFeatures->count())->toBeGreaterThan(0);
    expect($apiFeatures->count())->toBeGreaterThan(0);
    
    // Test search scope
    DynamicFeature::factory()->create(['name' => 'Blog System']);
    DynamicFeature::factory()->create(['name' => 'User Management']);
    
    $blogFeatures = DynamicFeature::search('blog')->get();
    $userFeatures = DynamicFeature::search('user')->get();
    
    expect($blogFeatures->count())->toBeGreaterThan(0);
    expect($userFeatures->count())->toBeGreaterThan(0);
}

/**
 * Assert that a feature version has the expected scopes
 */
function assertFeatureVersionHasScopes(): void
{
    // Test active scope
    FeatureVersion::factory()->active()->create();
    FeatureVersion::factory()->inactive()->create();
    
    $activeVersions = FeatureVersion::active()->get();
    $inactiveVersions = FeatureVersion::inactive()->get();
    
    expect($activeVersions->count())->toBeGreaterThan(0);
    expect($inactiveVersions->count())->toBeGreaterThan(0);
    
    // Test version scope
    FeatureVersion::factory()->create(['version' => '1.0.0']);
    FeatureVersion::factory()->create(['version' => '1.1.0']);
    
    $version1 = FeatureVersion::byVersion('1.0.0')->get();
    $version2 = FeatureVersion::byVersion('1.1.0')->get();
    
    expect($version1->count())->toBeGreaterThan(0);
    expect($version2->count())->toBeGreaterThan(0);
}

/**
 * Assert that a feature has the expected methods
 */
function assertFeatureHasMethods(DynamicFeature $feature): void
{
    // Test getter methods
    expect($feature->getRouteName())->toBeString();
    expect($feature->getControllerName())->toBeString();
    expect($feature->getModelName())->toBeString();
    expect($feature->getTableName())->toBeString();
    expect($feature->getViewPath())->toBeString();
    expect($feature->getApiRoutePrefix())->toBeString();
    expect($feature->getPermissions())->toBeArray();
    expect($feature->getMiddleware())->toBeArray();
    expect($feature->isApiEnabled())->toBeBool();
    expect($feature->hasDashboardWidget())->toBeBool();
    expect($feature->getFields())->toBeArray();
    
    // Test configuration methods
    expect($feature->getModelConfiguration())->toBeArray();
    expect($feature->getControllerConfiguration())->toBeArray();
    expect($feature->getViewConfiguration())->toBeArray();
    expect($feature->getRouteConfiguration())->toBeArray();
    expect($feature->getFullConfiguration())->toBeArray();
    
    // Test code methods
    expect($feature->getCodeFiles())->toBeArray();
    expect($feature->getModelCode())->toBeString();
    expect($feature->getControllerCode())->toBeString();
    expect($feature->getViewsCode())->toBeArray();
    expect($feature->getMigrationCode())->toBeString();
    expect($feature->getRouteCode())->toBeString();
    expect($feature->getApiRouteCode())->toBeString();
    expect($feature->getFullCodeSnapshot())->toBeArray();
    
    // Test stats method
    expect($feature->getStats())->toBeArray();
}

/**
 * Assert that a feature version has the expected methods
 */
function assertFeatureVersionHasMethods(FeatureVersion $version): void
{
    // Test getter methods
    expect($version->getCodeFiles())->toBeArray();
    expect($version->getModelCode())->toBeString();
    expect($version->getControllerCode())->toBeString();
    expect($version->getViewsCode())->toBeArray();
    expect($version->getMigrationCode())->toBeString();
    expect($version->getRouteCode())->toBeString();
    expect($version->getApiRouteCode())->toBeString();
    expect($version->getFullCodeSnapshot())->toBeArray();
    
    // Test info methods
    expect($version->getVersionInfo())->toBeArray();
    expect($version->getChangeSummary())->toBeArray();
}


