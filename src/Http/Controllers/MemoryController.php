<?php

namespace AiEditor\AiTextEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use AiEditor\AiTextEditor\Services\MemoryService;
use AiEditor\AiTextEditor\Models\EditorMemory;

class MemoryController
{
    public function __construct(
        protected MemoryService $memoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20);
        $tag = $request->input('tag');
        $search = $request->input('search');

        $query = EditorMemory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($tag) {
            $query->where('tag', $tag);
        }

        if ($search) {
            $query->search($search);
        }

        $memories = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $memories->map(function ($memory) {
                return [
                    'id' => $memory->id,
                    'content_preview' => $memory->content_preview,
                    'action' => $memory->action,
                    'tag' => $memory->tag,
                    'formatted_date' => $memory->formatted_date,
                    'relative_date' => $memory->relative_date,
                    'metadata' => $memory->metadata,
                ];
            }),
            'stats' => $this->memoryService->getStats(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'action' => 'required|string|in:edit,generate,summarize,complete,restore',
            'tag' => 'nullable|string|max:100',
            'metadata' => 'nullable|array',
        ]);

        $memory = $this->memoryService->store(
            $request->input('content'),
            $request->input('action'),
            $request->input('tag'),
            $request->input('metadata', [])
        );

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $memory->id,
                'content_preview' => $memory->content_preview,
                'action' => $memory->action,
                'tag' => $memory->tag,
                'formatted_date' => $memory->formatted_date,
                'relative_date' => $memory->relative_date,
                'metadata' => $memory->metadata,
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $memory = $this->memoryService->getVersion($id);

        if (!$memory) {
            return response()->json([
                'success' => false,
                'message' => 'Memory not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $memory->id,
                'content' => $memory->content,
                'action' => $memory->action,
                'tag' => $memory->tag,
                'formatted_date' => $memory->formatted_date,
                'relative_date' => $memory->relative_date,
                'metadata' => $memory->metadata,
                'created_at' => $memory->created_at,
                'updated_at' => $memory->updated_at,
            ],
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $memory = $this->memoryService->restore($id);

        if (!$memory) {
            return response()->json([
                'success' => false,
                'message' => 'Memory not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $memory->id,
                'content' => $memory->content,
                'action' => $memory->action,
                'tag' => $memory->tag,
                'formatted_date' => $memory->formatted_date,
                'relative_date' => $memory->relative_date,
                'metadata' => $memory->metadata,
            ],
            'message' => 'Content restored successfully',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->memoryService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Memory not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Memory deleted successfully',
        ]);
    }

    public function export(Request $request, int $id): JsonResponse
    {
        $format = $request->input('format', 'markdown');

        try {
            if ($format === 'markdown') {
                $content = $this->memoryService->exportToMarkdown($id);
                $filename = "memory-{$id}.md";
            } elseif ($format === 'pdf') {
                $content = $this->memoryService->exportToPdf($id);
                $filename = "memory-{$id}.html";
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid format. Supported: markdown, pdf',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'content' => $content,
                'filename' => $filename,
                'format' => $format,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'tag' => 'nullable|string',
        ]);

        $results = $this->memoryService->search(
            $request->input('query'),
            $request->input('tag')
        );

        return response()->json([
            'success' => true,
            'data' => $results->map(function ($memory) {
                return [
                    'id' => $memory->id,
                    'content_preview' => $memory->content_preview,
                    'action' => $memory->action,
                    'tag' => $memory->tag,
                    'formatted_date' => $memory->formatted_date,
                    'relative_date' => $memory->relative_date,
                    'metadata' => $memory->metadata,
                ];
            }),
        ]);
    }

    public function tags(): JsonResponse
    {
        $tags = $this->memoryService->getTags();

        return response()->json([
            'success' => true,
            'data' => $tags,
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = $this->memoryService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
