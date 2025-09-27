<?php

namespace AiEditor\AiTextEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use AiEditor\AiTextEditor\Services\StackService;

class StackController
{
    public function __construct(
        protected StackService $stackService
    ) {}

    public function index(): JsonResponse
    {
        $stacks = $this->stackService->getAvailableStacks();
        $stackDetails = [];

        foreach ($stacks as $stack) {
            $stackDetails[$stack] = $this->stackService->getStackInfo($stack);
        }

        return response()->json([
            'success' => true,
            'stacks' => $stackDetails
        ]);
    }

    public function switch(Request $request): JsonResponse
    {
        $request->validate([
            'stack' => 'required|string|in:' . implode(',', $this->stackService->getAvailableStacks())
        ]);

        $stack = $request->input('stack');
        
        // This would implement actual stack switching logic
        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => "Switched to {$stack} stack",
            'stack' => $stack
        ]);
    }

    public function status(): JsonResponse
    {
        // Get current stack status
        $currentStack = config('multi-stack.current_stack', 'blade-livewire');
        
        return response()->json([
            'success' => true,
            'current_stack' => $currentStack,
            'stack_info' => $this->stackService->getStackInfo($currentStack),
            'dependencies' => [
                'composer' => $this->stackService->getComposerDependencies($currentStack),
                'npm' => $this->stackService->getNpmDependencies($currentStack)
            ]
        ]);
    }
}
