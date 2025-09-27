<?php

namespace LaravelStarterKit\MultiStack\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LaravelStarterKit\MultiStack\Models\DynamicFeature;
use LaravelStarterKit\MultiStack\Models\FeatureVersion;

class AiFeatureBuilderService
{
    protected array $providers = [];
    protected string $defaultProvider;

    public function __construct()
    {
        $this->defaultProvider = config('multi-stack.ai.default_provider', 'openai');
        $this->initializeProviders();
    }

    protected function initializeProviders(): void
    {
        $this->providers = [
            'openai' => [
                'name' => 'OpenAI',
                'base_url' => 'https://api.openai.com/v1',
                'api_key' => config('multi-stack.ai.providers.openai.api_key'),
                'model' => config('multi-stack.ai.providers.openai.model', 'gpt-4'),
            ],
            'anthropic' => [
                'name' => 'Anthropic Claude',
                'base_url' => 'https://api.anthropic.com/v1',
                'api_key' => config('multi-stack.ai.providers.anthropic.api_key'),
                'model' => config('multi-stack.ai.providers.anthropic.model', 'claude-3-sonnet-20240229'),
            ],
            'google' => [
                'name' => 'Google Gemini',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'api_key' => config('multi-stack.ai.providers.google.api_key'),
                'model' => config('multi-stack.ai.providers.google.model', 'gemini-pro'),
            ],
        ];
    }

    public function generateFeature(string $prompt, string $category = 'custom', ?string $provider = null): array
    {
        $provider = $provider ?? $this->defaultProvider;
        
        try {
            $response = $this->callAiProvider($provider, $prompt, $category);
            
            if (!$response['success']) {
                return $response;
            }

            // Parse AI response and create feature
            $featureData = $this->parseFeatureResponse($response['content']);
            
            // Create dynamic feature
            $feature = $this->createDynamicFeature($featureData, $category, $prompt, $provider);
            
            // Generate feature code
            $this->generateFeatureCode($feature, $featureData);
            
            return [
                'success' => true,
                'feature' => $feature,
                'generated_code' => $this->getGeneratedCode($feature),
                'provider' => $provider,
                'model' => $response['model'] ?? null,
            ];
            
        } catch (\Exception $e) {
            Log::error('AI Feature Generation failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'prompt' => $prompt,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $provider,
            ];
        }
    }

    protected function callAiProvider(string $provider, string $prompt, string $category): array
    {
        $providerConfig = $this->providers[$provider] ?? null;
        
        if (!$providerConfig || !$providerConfig['api_key']) {
            throw new \Exception("AI provider '{$provider}' not configured");
        }

        $systemPrompt = $this->getFeatureGenerationPrompt($category);
        $fullPrompt = $systemPrompt . "\n\nUser Request: " . $prompt;

        switch ($provider) {
            case 'openai':
                return $this->callOpenAI($providerConfig, $fullPrompt);
            case 'anthropic':
                return $this->callAnthropic($providerConfig, $fullPrompt);
            case 'google':
                return $this->callGoogle($providerConfig, $fullPrompt);
            default:
                throw new \Exception("Unsupported AI provider: {$provider}");
        }
    }

    protected function callOpenAI(array $config, string $prompt): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $config['api_key'],
            'Content-Type' => 'application/json',
        ])->post($config['base_url'] . '/chat/completions', [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert Laravel developer and feature generator.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 4000,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'success' => true,
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'model' => $data['model'] ?? $config['model'],
            'usage' => $data['usage'] ?? null,
        ];
    }

    protected function callAnthropic(array $config, string $prompt): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $config['api_key'],
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->post($config['base_url'] . '/messages', [
            'model' => $config['model'],
            'max_tokens' => 4000,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Anthropic API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'success' => true,
            'content' => $data['content'][0]['text'] ?? '',
            'model' => $data['model'] ?? $config['model'],
            'usage' => $data['usage'] ?? null,
        ];
    }

    protected function callGoogle(array $config, string $prompt): array
    {
        $response = Http::post($config['base_url'] . '/models/' . $config['model'] . ':generateContent', [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 4000,
            ]
        ], [
            'key' => $config['api_key']
        ]);

        if ($response->failed()) {
            throw new \Exception('Google API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'success' => true,
            'content' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'model' => $config['model'],
            'usage' => null,
        ];
    }

    protected function getFeatureGenerationPrompt(string $category): string
    {
        return "You are an expert Laravel developer and feature generator. Generate a complete Laravel feature based on the user's request.

Category: {$category}

Please respond with a JSON object containing:
{
    \"name\": \"Feature Name\",
    \"description\": \"Brief description of the feature\",
    \"type\": \"crud|api|dashboard|component|integration|custom\",
    \"category\": \"{$category}\",
    \"configuration\": {
        \"model\": \"ModelName\",
        \"table\": \"table_name\",
        \"fields\": [
            {\"name\": \"field_name\", \"type\": \"string|integer|text|boolean|json\", \"nullable\": true/false, \"default\": \"default_value\"}
        ],
        \"permissions\": [\"permission1\", \"permission2\"],
        \"middleware\": [\"auth\", \"role:admin\"],
        \"api_enabled\": true/false,
        \"dashboard_widget\": true/false,
        \"routes\": {
            \"web\": [\"index\", \"create\", \"store\", \"show\", \"edit\", \"update\", \"destroy\"],
            \"api\": [\"index\", \"show\", \"store\", \"update\", \"destroy\"]
        }
    },
    \"code\": {
        \"model\": \"// Eloquent model code\",
        \"controller\": \"// Controller code\",
        \"migration\": \"// Migration code\",
        \"views\": {
            \"index\": \"// Index view code\",
            \"create\": \"// Create view code\",
            \"edit\": \"// Edit view code\",
            \"show\": \"// Show view code\"
        },
        \"routes\": \"// Route definitions\",
        \"api_routes\": \"// API route definitions\"
    }
}

Make sure the feature is production-ready and follows Laravel best practices.";
    }

    protected function parseFeatureResponse(string $content): array
    {
        // Extract JSON from the response
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
            $data = json_decode($jsonString, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }
        
        // Fallback parsing if JSON extraction fails
        return $this->fallbackParseFeatureResponse($content);
    }

    protected function fallbackParseFeatureResponse(string $content): array
    {
        // Extract name
        preg_match('/name[:\s]+([^\n,]+)/i', $content, $nameMatches);
        $name = $nameMatches[1] ?? 'Generated Feature';
        
        // Extract description
        preg_match('/description[:\s]+([^\n,]+)/i', $content, $descMatches);
        $description = $descMatches[1] ?? 'AI Generated Feature';
        
        // Extract type
        preg_match('/type[:\s]+([^\n,]+)/i', $content, $typeMatches);
        $type = $typeMatches[1] ?? 'custom';
        
        return [
            'name' => trim($name),
            'description' => trim($description),
            'type' => trim($type),
            'category' => 'custom',
            'configuration' => [
                'model' => 'GeneratedModel',
                'table' => 'generated_table',
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
                'model' => '// Model code will be generated',
                'controller' => '// Controller code will be generated',
                'migration' => '// Migration code will be generated',
                'views' => [
                    'index' => '// Index view code',
                    'create' => '// Create view code',
                    'edit' => '// Edit view code',
                    'show' => '// Show view code'
                ],
                'routes' => '// Route definitions',
                'api_routes' => '// API route definitions'
            ]
        ];
    }

    protected function createDynamicFeature(array $featureData, string $category, string $prompt, string $provider): DynamicFeature
    {
        return DynamicFeature::create([
            'name' => $featureData['name'],
            'slug' => Str::slug($featureData['name']),
            'description' => $featureData['description'],
            'type' => $featureData['type'],
            'category' => $category,
            'is_enabled' => true,
            'is_ai_generated' => true,
            'configuration' => $featureData['configuration'],
            'code_snapshot' => $featureData['code'],
            'metadata' => [
                'ai_prompt' => $prompt,
                'ai_provider' => $provider,
                'generated_at' => now(),
                'version' => '1.0.0',
            ],
        ]);
    }

    protected function generateFeatureCode(DynamicFeature $feature, array $featureData): void
    {
        // Generate Model
        $this->generateModel($feature, $featureData);
        
        // Generate Controller
        $this->generateController($feature, $featureData);
        
        // Generate Migration
        $this->generateMigration($feature, $featureData);
        
        // Generate Views
        $this->generateViews($feature, $featureData);
        
        // Generate Routes
        $this->generateRoutes($feature, $featureData);
        
        // Create version
        $this->createFeatureVersion($feature, '1.0.0', 'AI Generated Feature');
    }

    protected function generateModel(DynamicFeature $feature, array $featureData): void
    {
        $modelName = $featureData['configuration']['model'];
        $tableName = $featureData['configuration']['table'];
        $fields = $featureData['configuration']['fields'] ?? [];
        
        $fillable = collect($fields)->pluck('name')->toArray();
        $casts = collect($fields)->where('type', 'json')->pluck('name')->mapWithKeys(function ($field) {
            return [$field => 'array'];
        })->toArray();
        
        $modelContent = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\n\nclass {$modelName} extends Model\n{\n    use HasFactory;\n\n    protected \$fillable = " . var_export($fillable, true) . ";\n\n    protected \$casts = " . var_export($casts, true) . ";\n\n    protected \$table = '{$tableName}';\n}\n";
        
        $modelPath = app_path("Models/{$modelName}.php");
        \Illuminate\Support\Facades\File::put($modelPath, $modelContent);
    }

    protected function generateController(DynamicFeature $feature, array $featureData): void
    {
        $controllerName = $featureData['configuration']['model'] . 'Controller';
        $modelName = $featureData['configuration']['model'];
        $tableName = $featureData['configuration']['table'];
        
        $controllerContent = "<?php\n\nnamespace App\Http\Controllers;\n\nuse App\Models\\{$modelName};\nuse Illuminate\Http\Request;\nuse Illuminate\Http\JsonResponse;\n\nclass {$controllerName} extends Controller\n{\n    public function index()\n    {\n        \$items = {$modelName}::paginate(15);\n        return view('{$tableName}.index', compact('items'));\n    }\n\n    public function create()\n    {\n        return view('{$tableName}.create');\n    }\n\n    public function store(Request \$request)\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        {$modelName}::create(\$validated);\n\n        return redirect()->route('{$tableName}.index')\n            ->with('success', 'Item created successfully.');\n    }\n\n    public function show({$modelName} \$" . strtolower($modelName) . ")\n    {\n        return view('{$tableName}.show', compact('" . strtolower($modelName) . "'));\n    }\n\n    public function edit({$modelName} \$" . strtolower($modelName) . ")\n    {\n        return view('{$tableName}.edit', compact('" . strtolower($modelName) . "'));\n    }\n\n    public function update(Request \$request, {$modelName} \$" . strtolower($modelName) . ")\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        \$" . strtolower($modelName) . "->update(\$validated);\n\n        return redirect()->route('{$tableName}.index')\n            ->with('success', 'Item updated successfully.');\n    }\n\n    public function destroy({$modelName} \$" . strtolower($modelName) . ")\n    {\n        \$" . strtolower($modelName) . "->delete();\n\n        return redirect()->route('{$tableName}.index')\n            ->with('success', 'Item deleted successfully.');\n    }\n\n    // API Methods\n    public function apiIndex(): JsonResponse\n    {\n        \$items = {$modelName}::paginate(15);\n        return response()->json(\$items);\n    }\n\n    public function apiShow({$modelName} \$" . strtolower($modelName) . "): JsonResponse\n    {\n        return response()->json(\$" . strtolower($modelName) . ");\n    }\n\n    public function apiStore(Request \$request): JsonResponse\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        \$item = {$modelName}::create(\$validated);\n\n        return response()->json(\$item, 201);\n    }\n\n    public function apiUpdate(Request \$request, {$modelName} \$" . strtolower($modelName) . "): JsonResponse\n    {\n        \$validated = \$request->validate([\n            // Add validation rules here\n        ]);\n\n        \$" . strtolower($modelName) . "->update(\$validated);\n\n        return response()->json(\$" . strtolower($modelName) . ");\n    }\n\n    public function apiDestroy({$modelName} \$" . strtolower($modelName) . "): JsonResponse\n    {\n        \$" . strtolower($modelName) . "->delete();\n\n        return response()->json(['message' => 'Item deleted successfully.']);\n    }\n}\n";
        
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");
        \Illuminate\Support\Facades\File::put($controllerPath, $controllerContent);
    }

    protected function generateMigration(DynamicFeature $feature, array $featureData): void
    {
        $tableName = $featureData['configuration']['table'];
        $fields = $featureData['configuration']['fields'] ?? [];
        
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
        
        \Illuminate\Support\Facades\File::put($migrationPath, $migrationContent);
    }

    protected function generateViews(DynamicFeature $feature, array $featureData): void
    {
        $viewPath = resource_path("views/{$feature->slug}");
        \Illuminate\Support\Facades\File::makeDirectory($viewPath, 0755, true);
        
        $views = $featureData['code']['views'] ?? [];
        
        foreach ($views as $viewName => $viewContent) {
            if (!empty($viewContent)) {
                \Illuminate\Support\Facades\File::put($viewPath . "/{$viewName}.blade.php", $viewContent);
            }
        }
    }

    protected function generateRoutes(DynamicFeature $feature, array $featureData): void
    {
        $routes = $featureData['configuration']['routes'] ?? [];
        $tableName = $featureData['configuration']['table'];
        $controllerName = $featureData['configuration']['model'] . 'Controller';
        
        // Web routes
        if (!empty($routes['web'])) {
            $webRoutes = "// {$feature->name} Routes\n";
            $webRoutes .= "Route::resource('{$tableName}', App\\Http\\Controllers\\{$controllerName}::class);\n";
            
            $routesPath = base_path('routes/web.php');
            $existingContent = \Illuminate\Support\Facades\File::get($routesPath);
            \Illuminate\Support\Facades\File::put($routesPath, $existingContent . "\n" . $webRoutes);
        }
        
        // API routes
        if (!empty($routes['api'])) {
            $apiRoutes = "// {$feature->name} API Routes\n";
            $apiRoutes .= "Route::prefix('api/v1')->middleware(['auth:sanctum'])->group(function () {\n";
            $apiRoutes .= "    Route::get('{$tableName}', [App\\Http\\Controllers\\{$controllerName}::class, 'apiIndex']);\n";
            $apiRoutes .= "    Route::get('{$tableName}/{id}', [App\\Http\\Controllers\\{$controllerName}::class, 'apiShow']);\n";
            $apiRoutes .= "    Route::post('{$tableName}', [App\\Http\\Controllers\\{$controllerName}::class, 'apiStore']);\n";
            $apiRoutes .= "    Route::put('{$tableName}/{id}', [App\\Http\\Controllers\\{$controllerName}::class, 'apiUpdate']);\n";
            $apiRoutes .= "    Route::delete('{$tableName}/{id}', [App\\Http\\Controllers\\{$controllerName}::class, 'apiDestroy']);\n";
            $apiRoutes .= "});\n";
            
            $apiRoutesPath = base_path('routes/api.php');
            if (\Illuminate\Support\Facades\File::exists($apiRoutesPath)) {
                $existingContent = \Illuminate\Support\Facades\File::get($apiRoutesPath);
                \Illuminate\Support\Facades\File::put($apiRoutesPath, $existingContent . "\n" . $apiRoutes);
            }
        }
    }

    protected function createFeatureVersion(DynamicFeature $feature, string $version, string $description): FeatureVersion
    {
        return FeatureVersion::create([
            'feature_id' => $feature->id,
            'version' => $version,
            'description' => $description,
            'code_snapshot' => $feature->code_snapshot,
            'is_active' => true,
        ]);
    }

    protected function getGeneratedCode(DynamicFeature $feature): array
    {
        return [
            'model' => $feature->code_snapshot['model'] ?? null,
            'controller' => $feature->code_snapshot['controller'] ?? null,
            'migration' => $feature->code_snapshot['migration'] ?? null,
            'views' => $feature->code_snapshot['views'] ?? [],
            'routes' => $feature->code_snapshot['routes'] ?? null,
            'api_routes' => $feature->code_snapshot['api_routes'] ?? null,
        ];
    }

    public function getAvailableProviders(): array
    {
        return array_keys($this->providers);
    }

    public function getProviderInfo(string $provider): array
    {
        $config = $this->providers[$provider] ?? null;
        
        if (!$config) {
            throw new \Exception("Provider '{$provider}' not found");
        }

        return [
            'name' => $config['name'],
            'configured' => !empty($config['api_key']),
            'model' => $config['model'],
        ];
    }
}
