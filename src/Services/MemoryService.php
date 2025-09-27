<?php

namespace AiEditor\AiTextEditor\Services;

use AiEditor\AiTextEditor\Models\EditorMemory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemoryService
{
    public function store(string $content, string $action = 'edit', ?string $tag = null, array $metadata = []): EditorMemory
    {
        $memory = EditorMemory::create([
            'user_id' => Auth::id(),
            'content' => $content,
            'action' => $action,
            'tag' => $tag,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);

        // Clean up old versions if limit exceeded
        $this->cleanupOldVersions();

        return $memory;
    }

    public function getVersions(int $limit = 20, ?string $tag = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = EditorMemory::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($tag) {
            $query->where('tag', $tag);
        }

        return $query->limit($limit)->get();
    }

    public function getVersion(int $id): ?EditorMemory
    {
        return EditorMemory::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
    }

    public function restore(int $id): ?EditorMemory
    {
        $memory = $this->getVersion($id);
        
        if ($memory) {
            // Store current state before restoring
            $this->store($memory->content, 'restore', 'auto-save-before-restore');
        }

        return $memory;
    }

    public function delete(int $id): bool
    {
        $memory = EditorMemory::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if ($memory) {
            return $memory->delete();
        }

        return false;
    }

    public function search(string $query, ?string $tag = null): \Illuminate\Database\Eloquent\Collection
    {
        $searchQuery = EditorMemory::where('user_id', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('content', 'like', "%{$query}%")
                  ->orWhere('tag', 'like', "%{$query}%");
            });

        if ($tag) {
            $searchQuery->where('tag', $tag);
        }

        return $searchQuery->orderBy('created_at', 'desc')->get();
    }

    public function getTags(): array
    {
        return EditorMemory::where('user_id', Auth::id())
            ->whereNotNull('tag')
            ->distinct()
            ->pluck('tag')
            ->toArray();
    }

    public function getStats(): array
    {
        $total = EditorMemory::where('user_id', Auth::id())->count();
        $today = EditorMemory::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->count();
        $thisWeek = EditorMemory::where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return [
            'total_versions' => $total,
            'today' => $today,
            'this_week' => $thisWeek,
            'tags' => $this->getTags(),
        ];
    }

    protected function cleanupOldVersions(): void
    {
        $maxVersions = config('ai-text-editor.memory.max_versions', 50);
        
        $count = EditorMemory::where('user_id', Auth::id())->count();
        
        if ($count > $maxVersions) {
            $toDelete = $count - $maxVersions;
            
            EditorMemory::where('user_id', Auth::id())
                ->orderBy('created_at', 'asc')
                ->limit($toDelete)
                ->delete();
        }
    }

    public function exportToMarkdown(int $id): string
    {
        $memory = $this->getVersion($id);
        
        if (!$memory) {
            throw new \Exception('Version not found');
        }

        $content = "# AI Editor Memory\n\n";
        $content .= "**Date:** {$memory->created_at->format('Y-m-d H:i:s')}\n";
        $content .= "**Action:** {$memory->action}\n";
        $content .= "**Tag:** {$memory->tag ?? 'None'}\n\n";
        $content .= "---\n\n";
        $content .= $memory->content;

        return $content;
    }

    public function exportToPdf(int $id): string
    {
        // This would require a PDF library like dompdf or tcpdf
        // For now, return the content as HTML
        $memory = $this->getVersion($id);
        
        if (!$memory) {
            throw new \Exception('Version not found');
        }

        $html = "<!DOCTYPE html>
        <html>
        <head>
            <title>AI Editor Memory - {$memory->created_at->format('Y-m-d H:i:s')}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .metadata { color: #666; font-size: 14px; }
                .content { line-height: 1.6; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>AI Editor Memory</h1>
                <div class='metadata'>
                    <p><strong>Date:</strong> {$memory->created_at->format('Y-m-d H:i:s')}</p>
                    <p><strong>Action:</strong> {$memory->action}</p>
                    <p><strong>Tag:</strong> {$memory->tag ?? 'None'}</p>
                </div>
            </div>
            <div class='content'>
                {$memory->content}
            </div>
        </body>
        </html>";

        return $html;
    }
}
