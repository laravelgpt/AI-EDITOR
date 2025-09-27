<?php

namespace AiEditor\AiTextEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EditorMemory extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'action',
        'tag',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function getContentPreviewAttribute(): string
    {
        return \Str::limit(strip_tags($this->content), 100);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('M j, Y g:i A');
    }

    public function getRelativeDateAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->where('tag', $tag);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('content', 'like', "%{$search}%")
              ->orWhere('tag', 'like', "%{$search}%");
        });
    }
}
