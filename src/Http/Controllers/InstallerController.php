<?php

namespace AiEditor\AiTextEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use AiEditor\AiTextEditor\Services\InstallerService;
use AiEditor\AiTextEditor\Services\StackService;
use AiEditor\AiTextEditor\Services\ThemeService;

class InstallerController
{
    public function __construct(
        protected InstallerService $installerService,
        protected StackService $stackService,
        protected ThemeService $themeService
    ) {}

    public function index()
    {
        $stacks = $this->stackService->getAvailableStacks();
        $stackDetails = [];

        foreach ($stacks as $stack) {
            $stackDetails[$stack] = $this->stackService->getStackInfo($stack);
        }

        $themes = $this->themeService->getAvailableThemes();
        $themeDetails = [];

        foreach ($themes as $theme) {
            $themeDetails[$theme] = $this->themeService->getThemeInfo($theme);
        }

        return view('multi-stack::installer.index', [
            'stacks' => $stackDetails,
            'themes' => $themeDetails,
            'config' => config('multi-stack', [])
        ]);
    }

    public function selectStack(Request $request): JsonResponse
    {
        $request->validate([
            'stack' => 'required|string|in:' . implode(',', $this->stackService->getAvailableStacks())
        ]);

        $stack = $request->input('stack');
        $stackInfo = $this->stackService->getStackInfo($stack);

        return response()->json([
            'success' => true,
            'stack' => $stack,
            'info' => $stackInfo,
            'dependencies' => [
                'composer' => $this->stackService->getComposerDependencies($stack),
                'npm' => $this->stackService->getNpmDependencies($stack)
            ],
            'scripts' => $this->stackService->getPackageScripts($stack)
        ]);
    }

    public function install(Request $request): JsonResponse
    {
        $request->validate([
            'stack' => 'required|string|in:' . implode(',', $this->stackService->getAvailableStacks()),
            'theme' => 'required|string|in:' . implode(',', $this->themeService->getAvailableThemes()),
            'options' => 'nullable|array',
            'options.install_composer' => 'nullable|boolean',
            'options.install_npm' => 'nullable|boolean',
            'options.run_migrations' => 'nullable|boolean',
            'options.seed_database' => 'nullable|boolean',
            'options.setup_authentication' => 'nullable|boolean',
            'options.create_admin_user' => 'nullable|boolean',
        ]);

        $stack = $request->input('stack');
        $theme = $request->input('theme');
        $options = $request->input('options', []);

        // Set default options
        $options = array_merge([
            'install_composer' => true,
            'install_npm' => true,
            'run_migrations' => true,
            'seed_database' => true,
            'setup_authentication' => true,
            'create_admin_user' => true,
        ], $options);

        // Start installation in background
        $installationId = uniqid('install_');
        
        // Store installation job (in a real implementation, you'd use queues)
        session()->put("installation.{$installationId}", [
            'stack' => $stack,
            'theme' => $theme,
            'options' => $options,
            'status' => 'pending',
            'progress' => 0,
            'logs' => []
        ]);

        // Simulate installation (in real implementation, dispatch job)
        $this->simulateInstallation($installationId, $stack, $theme, $options);

        return response()->json([
            'success' => true,
            'installation_id' => $installationId,
            'message' => 'Installation started'
        ]);
    }

    public function progress(string $id): JsonResponse
    {
        $installation = session()->get("installation.{$id}");

        if (!$installation) {
            return response()->json([
                'success' => false,
                'message' => 'Installation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'installation' => $installation
        ]);
    }

    public function complete()
    {
        return view('multi-stack::installer.complete');
    }

    protected function simulateInstallation(string $id, string $stack, string $theme, array $options): void
    {
        // This is a simulation - in a real implementation, you'd use Laravel queues
        $steps = [
            'Installing dependencies...',
            'Generating scaffolding...',
            'Setting up authentication...',
            'Applying theme...',
            'Creating starter pages...',
            'Setting up routes...',
            'Running migrations...',
            'Seeding database...',
            'Creating admin user...',
            'Finalizing installation...'
        ];

        $totalSteps = count($steps);
        
        foreach ($steps as $index => $step) {
            // Update progress
            $progress = (($index + 1) / $totalSteps) * 100;
            
            $installation = session()->get("installation.{$id}", []);
            $installation['status'] = 'running';
            $installation['progress'] = $progress;
            $installation['current_step'] = $step;
            $installation['logs'][] = [
                'timestamp' => now()->toISOString(),
                'level' => 'info',
                'message' => $step
            ];
            
            session()->put("installation.{$id}", $installation);
            
            // Simulate work
            sleep(1);
        }

        // Mark as completed
        $installation = session()->get("installation.{$id}", []);
        $installation['status'] = 'completed';
        $installation['progress'] = 100;
        $installation['completed_at'] = now()->toISOString();
        $installation['logs'][] = [
            'timestamp' => now()->toISOString(),
            'level' => 'success',
            'message' => 'Installation completed successfully!'
        ];
        
        session()->put("installation.{$id}", $installation);
    }
}