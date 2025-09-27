<?php

namespace LaravelDynamicStarterKit\Advanced\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use LaravelDynamicStarterKit\Advanced\Models\Feature;
use LaravelDynamicStarterKit\Advanced\Models\FeatureVersion;
use LaravelDynamicStarterKit\Advanced\Services\AiService;

class FeatureService
{
    protected AiService $aiService;
    protected array $registeredFeatures = [];

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function createFeature(string $name, string $description, string $category, array $options = []): Feature
    {
        $feature = Feature::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $description,
            'category' => $category,
            'is_enabled' => $options['enabled'] ?? true,
            'is_ai_generated' => $options['ai_generated'] ?? false,
            'configuration' => $options['configuration'] ?? [],
            'metadata' => $options['metadata'] ?? [],
        ]);

        // Generate feature code
        $this->generateFeatureCode($feature, $options);

        // Create version
        $this->createFeatureVersion($feature, 'initial', 'Initial version');

        return $feature;
    }

    public function generateFeatureFromAi(string $prompt, string $category = 'custom'): Feature
    {
        $aiResponse = $this->aiService->generateFeature($prompt, $category);
        
        $feature = Feature::create([
            'name' => $aiResponse['name'],
            'slug' => Str::slug($aiResponse['name']),
            'description' => $aiResponse['description'],
            'category' => $category,
            'is_enabled' => true,
            'is_ai_generated' => true,
            'configuration' => $aiResponse['configuration'] ?? [],
            'metadata' => [
                'ai_prompt' => $prompt,
                'ai_provider' => $aiResponse['provider'] ?? 'openai',
                'generated_at' => now(),
            ],
        ]);

        // Generate the actual code
        $this->generateFeatureCode($feature, $aiResponse);

        // Create version
        $this->createFeatureVersion($feature, 'ai-generated', 'AI Generated Feature');

        return $feature;
    }

    protected function generateFeatureCode(Feature $feature, array $options): void
    {
        $featureType = $options['type'] ?? 'crud';
        
        switch ($featureType) {
            case 'crud':
                $this->generateCrudFeature($feature, $options);
                break;
            case 'api':
                $this->generateApiFeature($feature, $options);
                break;
            case 'dashboard':
                $this->generateDashboardFeature($feature, $options);
                break;
            case 'component':
                $this->generateComponentFeature($feature, $options);
                break;
            case 'integration':
                $this->generateIntegrationFeature($feature, $options);
                break;
            default:
                $this->generateCustomFeature($feature, $options);
        }
    }

    protected function generateCrudFeature(Feature $feature, array $options): void
    {
        $modelName = $options['model'] ?? Str::studly($feature->slug);
        $tableName = $options['table'] ?? Str::plural($feature->slug);
        
        // Generate Model
        $this->generateModel($modelName, $tableName, $options['fields'] ?? []);
        
        // Generate Migration
        $this->generateMigration($tableName, $options['fields'] ?? []);
        
        // Generate Controller
        $this->generateController($modelName, $options);
        
        // Generate Views
        $this->generateViews($feature, $options);
        
        // Generate Routes
        $this->generateRoutes($feature, $options);
        
        // Generate API Routes
        $this->generateApiRoutes($feature, $options);
    }

    protected function generateModel(string $modelName, string $tableName, array $fields): void
    {
        $modelPath = app_path("Models/{$modelName}.php");
        
        $fillable = collect($fields)->pluck('name')->toArray();
        $casts = collect($fields)->where('type', 'json')->pluck('name')->mapWithKeys(function ($field) {
            return [$field => 'array'];
        })->toArray();
        
        $modelContent = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\n\nclass {$modelName} extends Model\n{\n    use HasFactory;\n\n    protected \$fillable = " . var_export($fillable, true) . ";\n\n    protected \$casts = " . var_export($casts, true) . ";\n\n    protected \$table = '{$tableName}';\n}\n";
        
        File::put($modelPath, $modelContent);
    }

    protected function generateMigration(string $tableName, array $fields): void
    {
        $migrationName = "create_{$tableName}_table";
        $migrationPath = database_path("migrations/" . date('Y_m_d_His') . "_{$migrationName}.php");
        
        $migrationContent = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up()\n    {\n        Schema::create('{$tableName}', function (Blueprint \$table) {\n            \$table->id();\n";
        
        foreach ($fields as $field) {
            $migrationContent .= "            \$table->{$field['type']}('{$field['name']}')";
            if (isset($field['nullable']) && $field['nullable']) {
                $migrationContent .= "->nullable()";
            }
            if (isset($field['default'])) {
                $migrationContent .= "->default('{$field['default']}')";
            }
            $migrationContent .= ";\n";
        }
        
        $migrationContent .= "            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::dropIfExists('{$tableName}');\n    }\n};\n";
        
        File::put($migrationPath, $migrationContent);
    }

    protected function generateController(string $modelName, array $options): void
    {
        $controllerName = "{$modelName}Controller";
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");
        
        $controllerContent = "<?php\n\nnamespace App\Http\Controllers;\n\nuse App\Models\\{$modelName};\nuse Illuminate\Http\Request;\nuse Illuminate\Http\JsonResponse;\n\nclass {$controllerName} extends Controller\n{\n    public function index()\n    {\n        \$items = {$modelName}::paginate(15);\n        return view('{$modelName::lower()}.index', compact('items'));\n    }\n\n    public function create()\n    {\n        return view('{$modelName::lower()}.create');\n    }\n\n    public function store(Request \$request)\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        {$modelName}::create(\$validated);\n\n        return redirect()->route('{$modelName::lower()}.index')\n            ->with('success', 'Item created successfully.');\n    }\n\n    public function show({$modelName} \$" . strtolower($modelName) . ")\n    {\n        return view('{$modelName::lower()}.show', compact('" . strtolower($modelName) . "'));\n    }\n\n    public function edit({$modelName} \$" . strtolower($modelName) . ")\n    {\n        return view('{$modelName::lower()}.edit', compact('" . strtolower($modelName) . "'));\n    }\n\n    public function update(Request \$request, {$modelName} \$" . strtolower($modelName) . ")\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        \$" . strtolower($modelName) . "->update(\$validated);\n\n        return redirect()->route('{$modelName::lower()}.index')\n            ->with('success', 'Item updated successfully.');\n    }\n\n    public function destroy({$modelName} \$" . strtolower($modelName) . ")\n    {\n        \$" . strtolower($modelName) . "->delete();\n\n        return redirect()->route('{$modelName::lower()}.index')\n            ->with('success', 'Item deleted successfully.');\n    }\n\n    // API Methods\n    public function apiIndex(): JsonResponse\n    {\n        \$items = {$modelName}::paginate(15);\n        return response()->json(\$items);\n    }\n\n    public function apiShow({$modelName} \$" . strtolower($modelName) . "): JsonResponse\n    {\n        return response()->json(\$" . strtolower($modelName) . ");\n    }\n\n    public function apiStore(Request \$request): JsonResponse\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        \$item = {$modelName}::create(\$validated);\n\n        return response()->json(\$item, 201);\n    }\n\n    public function apiUpdate(Request \$request, {$modelName} \$" . strtolower($modelName) . "): JsonResponse\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        \$" . strtolower($modelName) . "->update(\$validated);\n\n        return response()->json(\$" . strtolower($modelName) . ");\n    }\n\n    public function apiDestroy({$modelName} \$" . strtolower($modelName) . "): JsonResponse\n    {\n        \$" . strtolower($modelName) . "->delete();\n\n        return response()->json(['message' => 'Item deleted successfully.']);\n    }\n}\n";
        
        File::put($controllerPath, $controllerContent);
    }

    protected function generateViews(Feature $feature, array $options): void
    {
        $viewPath = resource_path("views/{$feature->slug}");
        File::makeDirectory($viewPath, 0755, true);
        
        // Generate index view
        $this->generateIndexView($feature, $options);
        
        // Generate create view
        $this->generateCreateView($feature, $options);
        
        // Generate edit view
        $this->generateEditView($feature, $options);
        
        // Generate show view
        $this->generateShowView($feature, $options);
    }

    protected function generateIndexView(Feature $feature, array $options): void
    {
        $viewContent = "@extends('layouts.app')\n\n@section('content')\n<div class=\"container mx-auto px-4 py-8\">\n    <div class=\"flex justify-between items-center mb-6\">\n        <h1 class=\"text-3xl font-bold text-gray-900\">" . Str::title($feature->name) . "</h1>\n        <a href=\"{{ route('{$feature->slug}.create') }}\" class=\"bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700\">\n            Add New\n        </a>\n    </div>\n\n    <div class=\"bg-white shadow rounded-lg overflow-hidden\">\n        <table class=\"min-w-full divide-y divide-gray-200\">\n            <thead class=\"bg-gray-50\">\n                <tr>\n                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Name</th>\n                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Created</th>\n                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Actions</th>\n                </tr>\n            </thead>\n            <tbody class=\"bg-white divide-y divide-gray-200\">\n                @foreach(\$items as \$item)\n                <tr>\n                    <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900\">\n                        {{ \$item->name ?? \$item->title ?? 'Item #' . \$item->id }}\n                    </td>\n                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">\n                        {{ \$item->created_at->format('M d, Y') }}\n                    </td>\n                    <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium\">\n                        <a href=\"{{ route('{$feature->slug}.show', \$item) }}\" class=\"text-blue-600 hover:text-blue-900 mr-3\">View</a>\n                        <a href=\"{{ route('{$feature->slug}.edit', \$item) }}\" class=\"text-indigo-600 hover:text-indigo-900 mr-3\">Edit</a>\n                        <form action=\"{{ route('{$feature->slug}.destroy', \$item) }}\" method=\"POST\" class=\"inline\">\n                            @csrf\n                            @method('DELETE')\n                            <button type=\"submit\" class=\"text-red-600 hover:text-red-900\" onclick=\"return confirm('Are you sure?')\">Delete</button>\n                        </form>\n                    </td>\n                </tr>\n                @endforeach\n            </tbody>\n        </table>\n    </div>\n\n    <div class=\"mt-6\">\n        {{ \$items->links() }}\n    </div>\n</div>\n@endsection\n";
        
        File::put(resource_path("views/{$feature->slug}/index.blade.php"), $viewContent);
    }

    protected function generateCreateView(Feature $feature, array $options): void
    {
        $viewContent = "@extends('layouts.app')\n\n@section('content')\n<div class=\"container mx-auto px-4 py-8\">\n    <div class=\"max-w-2xl mx-auto\">\n        <h1 class=\"text-3xl font-bold text-gray-900 mb-6\">Create " . Str::title($feature->name) . "</h1>\n\n        <form action=\"{{ route('{$feature->slug}.store') }}\" method=\"POST\" class=\"bg-white shadow rounded-lg p-6\">\n            @csrf\n            \n            <div class=\"mb-4\">\n                <label for=\"name\" class=\"block text-sm font-medium text-gray-700 mb-2\">Name</label>\n                <input type=\"text\" name=\"name\" id=\"name\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500\" required>\n            </div>\n\n            <div class=\"flex justify-end space-x-3\">\n                <a href=\"{{ route('{$feature->slug}.index') }}\" class=\"px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50\">\n                    Cancel\n                </a>\n                <button type=\"submit\" class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700\">\n                    Create\n                </button>\n            </div>\n        </form>\n    </div>\n</div>\n@endsection\n";
        
        File::put(resource_path("views/{$feature->slug}/create.blade.php"), $viewContent);
    }

    protected function generateEditView(Feature $feature, array $options): void
    {
        $viewContent = "@extends('layouts.app')\n\n@section('content')\n<div class=\"container mx-auto px-4 py-8\">\n    <div class=\"max-w-2xl mx-auto\">\n        <h1 class=\"text-3xl font-bold text-gray-900 mb-6\">Edit " . Str::title($feature->name) . "</h1>\n\n        <form action=\"{{ route('{$feature->slug}.update', \${$feature->slug}) }}\" method=\"POST\" class=\"bg-white shadow rounded-lg p-6\">\n            @csrf\n            @method('PUT')\n            \n            <div class=\"mb-4\">\n                <label for=\"name\" class=\"block text-sm font-medium text-gray-700 mb-2\">Name</label>\n                <input type=\"text\" name=\"name\" id=\"name\" value=\"{{ \${$feature->slug}->name }}\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500\" required>\n            </div>\n\n            <div class=\"flex justify-end space-x-3\">\n                <a href=\"{{ route('{$feature->slug}.index') }}\" class=\"px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50\">\n                    Cancel\n                </a>\n                <button type=\"submit\" class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700\">\n                    Update\n                </button>\n            </div>\n        </form>\n    </div>\n</div>\n@endsection\n";
        
        File::put(resource_path("views/{$feature->slug}/edit.blade.php"), $viewContent);
    }

    protected function generateShowView(Feature $feature, array $options): void
    {
        $viewContent = "@extends('layouts.app')\n\n@section('content')\n<div class=\"container mx-auto px-4 py-8\">\n    <div class=\"max-w-4xl mx-auto\">\n        <div class=\"flex justify-between items-center mb-6\">\n            <h1 class=\"text-3xl font-bold text-gray-900\">" . Str::title($feature->name) . " Details</h1>\n            <div class=\"space-x-3\">\n                <a href=\"{{ route('{$feature->slug}.edit', \${$feature->slug}) }}\" class=\"bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700\">\n                    Edit\n                </a>\n                <a href=\"{{ route('{$feature->slug}.index') }}\" class=\"bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700\">\n                    Back to List\n                </a>\n            </div>\n        </div>\n\n        <div class=\"bg-white shadow rounded-lg p-6\">\n            <dl class=\"grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2\">\n                <div>\n                    <dt class=\"text-sm font-medium text-gray-500\">Name</dt>\n                    <dd class=\"mt-1 text-sm text-gray-900\">{{ \${$feature->slug}->name ?? 'N/A' }}</dd>\n                </div>\n                <div>\n                    <dt class=\"text-sm font-medium text-gray-500\">Created</dt>\n                    <dd class=\"mt-1 text-sm text-gray-900\">{{ \${$feature->slug}->created_at->format('M d, Y H:i') }}</dd>\n                </div>\n                <div>\n                    <dt class=\"text-sm font-medium text-gray-500\">Updated</dt>\n                    <dd class=\"mt-1 text-sm text-gray-900\">{{ \${$feature->slug}->updated_at->format('M d, Y H:i') }}</dd>\n                </div>\n            </dl>\n        </div>\n    </div>\n</div>\n@endsection\n";
        
        File::put(resource_path("views/{$feature->slug}/show.blade.php"), $viewContent);
    }

    protected function generateRoutes(Feature $feature, array $options): void
    {
        $routesContent = "// {$feature->name} Routes\n";
        $routesContent .= "Route::resource('{$feature->slug}', App\Http\Controllers\\" . Str::studly($feature->slug) . "Controller::class);\n";
        
        // Append to routes file
        $routesPath = base_path('routes/web.php');
        $existingContent = File::get($routesPath);
        File::put($routesPath, $existingContent . "\n" . $routesContent);
    }

    protected function generateApiRoutes(Feature $feature, array $options): void
    {
        $apiRoutesContent = "// {$feature->name} API Routes\n";
        $apiRoutesContent .= "Route::prefix('api/v1')->middleware(['auth:sanctum'])->group(function () {\n";
        $apiRoutesContent .= "    Route::get('{$feature->slug}', [App\Http\Controllers\\" . Str::studly($feature->slug) . "Controller::class, 'apiIndex']);\n";
        $apiRoutesContent .= "    Route::get('{$feature->slug}/{id}', [App\Http\Controllers\\" . Str::studly($feature->slug) . "Controller::class, 'apiShow']);\n";
        $apiRoutesContent .= "    Route::post('{$feature->slug}', [App\Http\Controllers\\" . Str::studly($feature->slug) . "Controller::class, 'apiStore']);\n";
        $apiRoutesContent .= "    Route::put('{$feature->slug}/{id}', [App\Http\Controllers\\" . Str::studly($feature->slug) . "Controller::class, 'apiUpdate']);\n";
        $apiRoutesContent .= "    Route::delete('{$feature->slug}/{id}', [App\Http\Controllers\\" . Str::studly($feature->slug) . "Controller::class, 'apiDestroy']);\n";
        $apiRoutesContent .= "});\n";
        
        // Append to API routes file
        $apiRoutesPath = base_path('routes/api.php');
        if (File::exists($apiRoutesPath)) {
            $existingContent = File::get($apiRoutesPath);
            File::put($apiRoutesPath, $existingContent . "\n" . $apiRoutesContent);
        }
    }

    protected function createFeatureVersion(Feature $feature, string $version, string $description): FeatureVersion
    {
        return FeatureVersion::create([
            'feature_id' => $feature->id,
            'version' => $version,
            'description' => $description,
            'code_snapshot' => $this->getFeatureCodeSnapshot($feature),
            'is_active' => true,
        ]);
    }

    protected function getFeatureCodeSnapshot(Feature $feature): array
    {
        // This would capture the current state of all generated files
        return [
            'model' => File::exists(app_path("Models/{$feature->name}.php")) ? File::get(app_path("Models/{$feature->name}.php")) : null,
            'controller' => File::exists(app_path("Http/Controllers/{$feature->name}Controller.php")) ? File::get(app_path("Http/Controllers/{$feature->name}Controller.php")) : null,
            'views' => $this->getViewsSnapshot($feature),
        ];
    }

    protected function getViewsSnapshot(Feature $feature): array
    {
        $views = [];
        $viewPath = resource_path("views/{$feature->slug}");
        
        if (File::exists($viewPath)) {
            $files = File::allFiles($viewPath);
            foreach ($files as $file) {
                $views[$file->getRelativePathname()] = $file->getContents();
            }
        }
        
        return $views;
    }

    public function registerDynamicRoutes(): void
    {
        $enabledFeatures = Feature::where('is_enabled', true)->get();
        
        foreach ($enabledFeatures as $feature) {
            $this->registerFeatureRoutes($feature);
        }
    }

    protected function registerFeatureRoutes(Feature $feature): void
    {
        // Register feature-specific routes
        Route::middleware(['web', 'auth'])
            ->prefix($feature->slug)
            ->name($feature->slug . '.')
            ->group(function () use ($feature) {
                Route::get('/', [App\Http\Controllers::class . "\\" . Str::studly($feature->slug) . "Controller", 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers::class . "\\" . Str::studly($feature->slug) . "Controller", 'create'])->name('create');
                Route::post('/', [App\Http\Controllers::class . "\\" . Str::studly($feature->slug) . "Controller", 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers::class . "\\" . Str::studly($feature->slug) . "Controller", 'show'])->name('show');
                Route::get('/{id}/edit', [App\Http\Controllers::class . "\\" . Str::studly($feature->slug) . "Controller", 'edit'])->name('edit');
                Route::put('/{id}', [App\Http\Controllers::class . "\\" . Str::studly($feature->slug) . "Controller", 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers::class . "\\" . Str::studly($feature->slug) . "Controller", 'destroy'])->name('destroy');
            });
    }

    public function toggleFeature(Feature $feature): bool
    {
        $feature->is_enabled = !$feature->is_enabled;
        $feature->save();
        
        if ($feature->is_enabled) {
            $this->registerFeatureRoutes($feature);
        }
        
        return $feature->is_enabled;
    }

    public function regenerateFeature(Feature $feature): Feature
    {
        // Create new version before regeneration
        $this->createFeatureVersion($feature, 'regenerated', 'Feature regenerated');
        
        // Regenerate the feature code
        $this->generateFeatureCode($feature, $feature->configuration);
        
        return $feature;
    }

    public function getFeatureStats(): array
    {
        return [
            'total_features' => Feature::count(),
            'enabled_features' => Feature::where('is_enabled', true)->count(),
            'ai_generated_features' => Feature::where('is_ai_generated', true)->count(),
            'features_by_category' => Feature::groupBy('category')->selectRaw('category, count(*) as count')->get()->pluck('count', 'category'),
        ];
    }
}
