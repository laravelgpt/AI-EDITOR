<?php

namespace AiEditor\AiTextEditor\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AiEditor\AiTextEditor\Models\DynamicFeature;
use AiEditor\AiTextEditor\Models\FeatureVersion;

class DynamicFeatureFactory extends Factory
{
    protected $model = DynamicFeature::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['crud', 'api', 'dashboard', 'component', 'integration', 'custom']),
            'category' => $this->faker->randomElement(['authentication', 'content_management', 'ecommerce', 'communication', 'analytics', 'custom']),
            'is_enabled' => $this->faker->boolean(80),
            'is_ai_generated' => $this->faker->boolean(30),
            'configuration' => [
                'model' => $this->faker->word() . 'Model',
                'table' => $this->faker->word() . '_table',
                'fields' => [
                    ['name' => 'title', 'type' => 'string', 'nullable' => false],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                ],
                'permissions' => $this->faker->randomElements(['view', 'create', 'edit', 'delete'], 2),
                'middleware' => $this->faker->randomElements(['auth', 'role:admin', 'permission:manage'], 1),
                'api_enabled' => $this->faker->boolean(70),
                'dashboard_widget' => $this->faker->boolean(40),
                'routes' => [
                    'web' => $this->faker->randomElements(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'], 4),
                    'api' => $this->faker->randomElements(['index', 'show', 'store', 'update', 'destroy'], 3),
                ]
            ],
            'code_snapshot' => [
                'model' => '// Model code for ' . $this->faker->word(),
                'controller' => '// Controller code for ' . $this->faker->word(),
                'migration' => '// Migration code for ' . $this->faker->word(),
                'views' => [
                    'index' => '// Index view code',
                    'create' => '// Create view code',
                    'edit' => '// Edit view code',
                    'show' => '// Show view code'
                ],
                'routes' => '// Route definitions',
                'api_routes' => '// API route definitions'
            ],
            'metadata' => [
                'ai_provider' => $this->faker->randomElement(['openai', 'anthropic', 'google']),
                'ai_prompt' => 'Create a ' . $this->faker->word() . ' system',
                'generated_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('c'),
                'version' => $this->faker->semver(),
            ],
        ];
    }

    public function aiGenerated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_ai_generated' => true,
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'ai_provider' => $this->faker->randomElement(['openai', 'anthropic', 'google']),
                'ai_prompt' => 'Create a ' . $this->faker->word() . ' system with ' . $this->faker->word(),
                'generated_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('c'),
                'version' => '1.0.0',
            ]),
        ]);
    }

    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => true,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => false,
        ]);
    }

    public function crud(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'crud',
            'configuration' => array_merge($attributes['configuration'] ?? [], [
                'routes' => [
                    'web' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'],
                    'api' => ['index', 'show', 'store', 'update', 'destroy']
                ]
            ]),
        ]);
    }

    public function api(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'api',
            'configuration' => array_merge($attributes['configuration'] ?? [], [
                'api_enabled' => true,
                'routes' => [
                    'web' => [],
                    'api' => ['index', 'show', 'store', 'update', 'destroy']
                ]
            ]),
        ]);
    }

    public function dashboard(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'dashboard',
            'configuration' => array_merge($attributes['configuration'] ?? [], [
                'dashboard_widget' => true,
                'routes' => [
                    'web' => ['index'],
                    'api' => ['index']
                ]
            ]),
        ]);
    }
}

class FeatureVersionFactory extends Factory
{
    protected $model = FeatureVersion::class;

    public function definition(): array
    {
        return [
            'feature_id' => DynamicFeature::factory(),
            'version' => $this->faker->semver(),
            'description' => $this->faker->sentence(),
            'code_snapshot' => [
                'model' => '// Model code version ' . $this->faker->semver(),
                'controller' => '// Controller code version ' . $this->faker->semver(),
                'migration' => '// Migration code version ' . $this->faker->semver(),
                'views' => [
                    'index' => '// Index view version ' . $this->faker->semver(),
                    'create' => '// Create view version ' . $this->faker->semver(),
                    'edit' => '// Edit view version ' . $this->faker->semver(),
                    'show' => '// Show view version ' . $this->faker->semver()
                ],
                'routes' => '// Route definitions version ' . $this->faker->semver(),
                'api_routes' => '// API route definitions version ' . $this->faker->semver()
            ],
            'is_active' => $this->faker->boolean(20),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function major(): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => $this->faker->numberBetween(1, 5) . '.0.0',
            'description' => 'Major version update',
        ]);
    }

    public function minor(): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => '1.' . $this->faker->numberBetween(1, 10) . '.0',
            'description' => 'Minor version update',
        ]);
    }

    public function patch(): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => '1.0.' . $this->faker->numberBetween(1, 20),
            'description' => 'Patch version update',
        ]);
    }
}
