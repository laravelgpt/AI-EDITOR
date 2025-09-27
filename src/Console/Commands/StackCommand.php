<?php

namespace LaravelStarterKit\MultiStack\Console\Commands;

use Illuminate\Console\Command;
use LaravelStarterKit\MultiStack\Services\StackService;

class StackCommand extends Command
{
    protected $signature = 'multi-stack:stacks 
                            {stack? : Show details for a specific stack}
                            {--switch= : Switch to a different stack}';

    protected $description = 'List available stacks or show details for a specific stack';

    public function handle(StackService $stackService): int
    {
        $stack = $this->argument('stack');
        $switchTo = $this->option('switch');

        if ($switchTo) {
            return $this->switchStack($switchTo, $stackService);
        }

        if ($stack) {
            return $this->showStackDetails($stack, $stackService);
        }

        return $this->listStacks($stackService);
    }

    protected function listStacks(StackService $stackService): int
    {
        $this->info('📦 Available Laravel Multi-Stack Options');
        $this->newLine();

        $stacks = $stackService->getAvailableStacks();

        foreach ($stacks as $stackKey) {
            $stackInfo = $stackService->getStackInfo($stackKey);
            
            $this->line("<comment>{$stackKey}</comment>");
            $this->line("  Name: {$stackInfo['name']}");
            $this->line("  Description: {$stackInfo['description']}");
            $this->line("  Icon: {$stackInfo['icon']}");
            $this->line("  Color: {$stackInfo['color']}");
            
            if (isset($stackInfo['features'])) {
                $this->line("  Features: " . implode(', ', $stackInfo['features']));
            }
            
            $this->newLine();
        }

        $this->info('Use "php artisan multi-stack:stacks <stack>" to see detailed information about a specific stack.');
        
        return 0;
    }

    protected function showStackDetails(string $stack, StackService $stackService): int
    {
        if (!$stackService->isValidStack($stack)) {
            $this->error("Invalid stack: {$stack}");
            $this->info('Available stacks: ' . implode(', ', $stackService->getAvailableStacks()));
            return 1;
        }

        $stackInfo = $stackService->getStackInfo($stack);

        $this->info("📦 Stack Details: {$stackInfo['name']}");
        $this->newLine();

        $this->line("Description: {$stackInfo['description']}");
        $this->line("Icon: {$stackInfo['icon']}");
        $this->line("Color: {$stackInfo['color']}");
        $this->newLine();

        // Show dependencies
        $composerDeps = $stackService->getComposerDependencies($stack);
        if (!empty($composerDeps)) {
            $this->info('Composer Dependencies:');
            foreach ($composerDeps as $dep) {
                $this->line("  - {$dep}");
            }
            $this->newLine();
        }

        $npmDeps = $stackService->getNpmDependencies($stack);
        if (!empty($npmDeps)) {
            $this->info('NPM Dependencies:');
            foreach ($npmDeps as $dep) {
                $this->line("  - {$dep}");
            }
            $this->newLine();
        }

        // Show features
        if (isset($stackInfo['features'])) {
            $this->info('Features:');
            foreach ($stackInfo['features'] as $feature) {
                $this->line("  - {$feature}");
            }
            $this->newLine();
        }

        // Show package scripts
        $scripts = $stackService->getPackageScripts($stack);
        if (!empty($scripts)) {
            $this->info('Package Scripts:');
            foreach ($scripts as $script => $command) {
                $this->line("  {$script}: {$command}");
            }
            $this->newLine();
        }

        $this->info("To install this stack, run: php artisan multi-stack:install --stack={$stack}");

        return 0;
    }

    protected function switchStack(string $stack, StackService $stackService): int
    {
        if (!$stackService->isValidStack($stack)) {
            $this->error("Invalid stack: {$stack}");
            $this->info('Available stacks: ' . implode(', ', $stackService->getAvailableStacks()));
            return 1;
        }

        $this->info("Switching to stack: {$stack}");
        
        // This would implement stack switching logic
        // For now, just show a message
        $this->warn("Stack switching functionality is not yet implemented.");
        $this->info("To install a new stack, run: php artisan multi-stack:install --stack={$stack}");

        return 0;
    }
}