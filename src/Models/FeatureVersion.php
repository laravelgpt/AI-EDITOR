<?php

namespace LaravelStarterKit\MultiStack\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureVersion extends Model
{
    protected $fillable = [
        'feature_id',
        'version',
        'description',
        'code_snapshot',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'code_snapshot' => 'array',
        'is_active' => 'boolean',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(DynamicFeature::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByVersion($query, string $version)
    {
        return $query->where('version', $version);
    }

    public function activate(): bool
    {
        // Deactivate other versions
        $this->feature->versions()->update(['is_active' => false]);
        
        // Activate this version
        $this->is_active = true;
        $this->save();
        
        return true;
    }

    public function deactivate(): bool
    {
        $this->is_active = false;
        $this->save();
        
        return true;
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

    public function restore(): bool
    {
        $codeSnapshot = $this->getFullCodeSnapshot();
        
        // Restore model
        if ($codeSnapshot['model']) {
            $modelPath = app_path("Models/{$this->feature->getModelName()}.php");
            \Illuminate\Support\Facades\File::put($modelPath, $codeSnapshot['model']);
        }
        
        // Restore controller
        if ($codeSnapshot['controller']) {
            $controllerPath = app_path("Http/Controllers/{$this->feature->getControllerName()}.php");
            \Illuminate\Support\Facades\File::put($controllerPath, $codeSnapshot['controller']);
        }
        
        // Restore views
        if ($codeSnapshot['views']) {
            $viewPath = resource_path("views/{$this->feature->getViewPath()}");
            \Illuminate\Support\Facades\File::makeDirectory($viewPath, 0755, true);
            
            foreach ($codeSnapshot['views'] as $viewFile => $content) {
                \Illuminate\Support\Facades\File::put($viewPath . '/' . $viewFile, $content);
            }
        }
        
        // Restore routes
        if ($codeSnapshot['routes']) {
            $routesPath = base_path('routes/web.php');
            $existingContent = \Illuminate\Support\Facades\File::get($routesPath);
            \Illuminate\Support\Facades\File::put($routesPath, $existingContent . "\n" . $codeSnapshot['routes']);
        }
        
        // Restore API routes
        if ($codeSnapshot['api_routes']) {
            $apiRoutesPath = base_path('routes/api.php');
            $existingContent = \Illuminate\Support\Facades\File::get($apiRoutesPath);
            \Illuminate\Support\Facades\File::put($apiRoutesPath, $existingContent . "\n" . $codeSnapshot['api_routes']);
        }
        
        return true;
    }

    public function getVersionInfo(): array
    {
        return [
            'id' => $this->id,
            'version' => $this->version,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'created_by' => $this->creator?->name,
            'feature_name' => $this->feature->name,
            'code_files_count' => count($this->getCodeFiles()),
        ];
    }

    public function getDiffWithVersion(FeatureVersion $otherVersion): array
    {
        $currentCode = $this->getFullCodeSnapshot();
        $otherCode = $otherVersion->getFullCodeSnapshot();
        
        $diff = [];
        
        foreach ($currentCode as $fileType => $currentContent) {
            $otherContent = $otherCode[$fileType] ?? null;
            
            if ($currentContent !== $otherContent) {
                $diff[$fileType] = [
                    'current' => $currentContent,
                    'other' => $otherContent,
                    'has_changes' => true,
                ];
            } else {
                $diff[$fileType] = [
                    'has_changes' => false,
                ];
            }
        }
        
        return $diff;
    }

    public function getChangeSummary(): array
    {
        $codeSnapshot = $this->getFullCodeSnapshot();
        
        return [
            'total_files' => count($codeSnapshot),
            'model_changed' => !empty($codeSnapshot['model']),
            'controller_changed' => !empty($codeSnapshot['controller']),
            'views_changed' => !empty($codeSnapshot['views']),
            'routes_changed' => !empty($codeSnapshot['routes']),
            'api_routes_changed' => !empty($codeSnapshot['api_routes']),
            'migration_changed' => !empty($codeSnapshot['migration']),
        ];
    }
}