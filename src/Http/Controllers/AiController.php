<?php

namespace AiEditor\AiTextEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use AiEditor\AiTextEditor\Services\AiService;
use AiEditor\AiTextEditor\Services\MemoryService;

class AiController
{
    public function __construct(
        protected AiService $aiService,
        protected MemoryService $memoryService
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:10000',
            'provider' => 'nullable|string|in:openai,anthropic,google,huggingface,openrouter',
            'options' => 'nullable|array',
            'save_to_memory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->aiService->generate(
            $request->input('prompt'),
            $request->input('provider'),
            $request->input('options', [])
        );

        // Save to memory if requested
        if ($request->input('save_to_memory', true) && $result['success']) {
            $this->memoryService->store(
                $result['content'],
                'generate',
                'ai-generated',
                [
                    'provider' => $result['provider'],
                    'model' => $result['model'] ?? null,
                    'prompt' => $request->input('prompt'),
                ]
            );
        }

        return response()->json($result);
    }

    public function edit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:50000',
            'instruction' => 'required|string|max:2000',
            'provider' => 'nullable|string|in:openai,anthropic,google,huggingface,openrouter',
            'options' => 'nullable|array',
            'save_to_memory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->aiService->edit(
            $request->input('text'),
            $request->input('instruction'),
            $request->input('provider'),
            $request->input('options', [])
        );

        // Save to memory if requested
        if ($request->input('save_to_memory', true) && $result['success']) {
            $this->memoryService->store(
                $result['content'],
                'edit',
                'ai-edited',
                [
                    'provider' => $result['provider'],
                    'model' => $result['model'] ?? null,
                    'original_text' => $request->input('text'),
                    'instruction' => $request->input('instruction'),
                ]
            );
        }

        return response()->json($result);
    }

    public function summarize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:50000',
            'provider' => 'nullable|string|in:openai,anthropic,google,huggingface,openrouter',
            'options' => 'nullable|array',
            'save_to_memory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->aiService->summarize(
            $request->input('text'),
            $request->input('provider'),
            $request->input('options', [])
        );

        // Save to memory if requested
        if ($request->input('save_to_memory', true) && $result['success']) {
            $this->memoryService->store(
                $result['content'],
                'summarize',
                'ai-summarized',
                [
                    'provider' => $result['provider'],
                    'model' => $result['model'] ?? null,
                    'original_text' => $request->input('text'),
                ]
            );
        }

        return response()->json($result);
    }

    public function complete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:50000',
            'provider' => 'nullable|string|in:openai,anthropic,google,huggingface,openrouter',
            'options' => 'nullable|array',
            'save_to_memory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->aiService->complete(
            $request->input('text'),
            $request->input('provider'),
            $request->input('options', [])
        );

        // Save to memory if requested
        if ($request->input('save_to_memory', true) && $result['success']) {
            $this->memoryService->store(
                $result['content'],
                'complete',
                'ai-completed',
                [
                    'provider' => $result['provider'],
                    'model' => $result['model'] ?? null,
                    'original_text' => $request->input('text'),
                ]
            );
        }

        return response()->json($result);
    }
}
