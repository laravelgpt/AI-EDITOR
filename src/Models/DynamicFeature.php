<?php

namespace LaravelStarterKit\MultiStack\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class DynamicFeature extends Model
{
    protected $fillable = [
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

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_ai_generated' => 'boolean',
        'configuration' => 'array',
        'code_snapshot' => 'array',
        'metadata' => 'array',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(FeatureVersion::class);
    }

    public function currentVersion(): HasMany
    {
        return $this->hasMany(FeatureVersion::class)->where('is_active', true);
    }

    public function getStatusAttribute(): string
    {
        return $this->is_enabled ? 'enabled' : 'disabled';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_enabled ? 'green' : 'red';
    }

    public function getCategoryNameAttribute(): string
    {
        $categories = config('multi-stack.feature_categories', []);
        return $categories[$this->category]['name'] ?? ucfirst($this->category);
    }

    public function getCategoryIconAttribute(): string
    {
        $categories = config('multi-stack.feature_categories', []);
        return $categories[$this->category]['icon'] ?? 'puzzle-piece';
    }

    public function getCategoryColorAttribute(): string
    {
        $categories = config('multi-stack.feature_categories', []);
        return $categories[$this->category]['color'] ?? 'gray';
    }

    public function getAiProviderAttribute(): ?string
    {
        return $this->metadata['ai_provider'] ?? null;
    }

    public function getAiPromptAttribute(): ?string
    {
        return $this->metadata['ai_prompt'] ?? null;
    }

    public function getGeneratedAtAttribute(): ?string
    {
        return $this->metadata['generated_at'] ?? null;
    }

    public function getVersionAttribute(): ?string
    {
        return $this->metadata['version'] ?? null;
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeDisabled($query)
    {
        return $query->where('is_enabled', false);
    }

    public function scopeAiGenerated($query)
    {
        return $query->where('is_ai_generated', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    public function toggle(): bool
    {
        $this->is_enabled = !$this->is_enabled;
        $this->save();
        
        return $this->is_enabled;
    }

    public function enable(): bool
    {
        $this->is_enabled = true;
        $this->save();
        
        return true;
    }

    public function disable(): bool
    {
        $this->is_enabled = false;
        $this->save();
        
        return true;
    }

    public function getRouteName(): string
    {
        return $this->slug;
    }

    public function getControllerName(): string
    {
        return Str::studly($this->slug) . 'Controller';
    }

    public function getModelName(): string
    {
        return $this->configuration['model'] ?? Str::studly($this->slug);
    }

    public function getTableName(): string
    {
        return $this->configuration['table'] ?? Str::plural($this->slug);
    }

    public function getViewPath(): string
    {
        return $this->slug;
    }

    public function getApiRoutePrefix(): string
    {
        return "api/v1/{$this->slug}";
    }

    public function getPermissions(): array
    {
        return $this->configuration['permissions'] ?? [];
    }

    public function getMiddleware(): array
    {
        return $this->configuration['middleware'] ?? ['auth'];
    }

    public function isApiEnabled(): bool
    {
        return $this->configuration['api_enabled'] ?? false;
    }

    public function hasDashboardWidget(): bool
    {
        return $this->configuration['dashboard_widget'] ?? false;
    }

    public function getFields(): array
    {
        return $this->configuration['fields'] ?? [];
    }

    public function getModelConfiguration(): array
    {
        return [
            'model' => $this->getModelName(),
            'table' => $this->getTableName(),
            'fields' => $this->getFields(),
        ];
    }

    public function getControllerConfiguration(): array
    {
        return [
            'name' => $this->getControllerName(),
            'namespace' => 'App\\Http\\Controllers',
            'methods' => $this->configuration['routes']['web'] ?? ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'],
            'api_methods' => $this->configuration['routes']['api'] ?? ['apiIndex', 'apiShow', 'apiStore', 'apiUpdate', 'apiDestroy'],
        ];
    }

    public function getViewConfiguration(): array
    {
        return [
            'path' => $this->getViewPath(),
            'views' => array_keys($this->code_snapshot['views'] ?? []),
            'layout' => 'layouts.app',
        ];
    }

    public function getRouteConfiguration(): array
    {
        return [
            'web' => [
                'prefix' => $this->slug,
                'name' => $this->slug . '.',
                'middleware' => $this->getMiddleware(),
            ],
            'api' => [
                'prefix' => $this->getApiRoutePrefix(),
                'middleware' => ['api', 'auth:sanctum'],
            ],
        ];
    }

    public function getFullConfiguration(): array
    {
        return [
            'feature' => $this->toArray(),
            'model' => $this->getModelConfiguration(),
            'controller' => $this->getControllerConfiguration(),
            'views' => $this->getViewConfiguration(),
            'routes' => $this->getRouteConfiguration(),
        ];
    }

    public function getCodeFiles(): array
    {
        return $this->code_snapshot ?? [];
    }

    public function getModelCode(): ?string
    {
        return $this->code_snapshot['model'] ?? null;
    }

    public function getControllerCode(): ?string
    {
        return $this->code_snapshot['controller'] ?? null;
    }

    public function getViewsCode(): array
    {
        return $this->code_snapshot['views'] ?? [];
    }

    public function getMigrationCode(): ?string
    {
        return $this->code_snapshot['migration'] ?? null;
    }

    public function getRouteCode(): ?string
    {
        return $this->code_snapshot['routes'] ?? null;
    }

    public function getApiRouteCode(): ?string
    {
        return $this->code_snapshot['api_routes'] ?? null;
    }

    public function getFullCodeSnapshot(): array
    {
        return [
            'model' => $this->getModelCode(),
            'controller' => $this->getControllerCode(),
            'views' => $this->getViewsCode(),
            'migration' => $this->getMigrationCode(),
            'routes' => $this->getRouteCode(),
            'api_routes' => $this->getApiRouteCode(),
        ];
    }

    public function getStats(): array
    {
        return [
            'total_versions' => $this->versions()->count(),
            'active_version' => $this->currentVersion()->first()?->version,
            'last_updated' => $this->updated_at,
            'is_ai_generated' => $this->is_ai_generated,
            'ai_provider' => $this->ai_provider,
        ];
    }
}
