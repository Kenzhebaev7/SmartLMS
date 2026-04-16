<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Thread extends Model
{
    public const TAG_PROJECT = 'project';
    public const TAG_QUESTION = 'question';
    public const TAG_HOMEWORK = 'homework';

    protected $fillable = ['user_id', 'section_id', 'title', 'body', 'lesson_id', 'hidden_at', 'tag', 'is_pinned'];

    protected function casts(): array
    {
        return [
            'hidden_at' => 'datetime',
            'is_pinned' => 'boolean',
        ];
    }

    public static function tags(): array
    {
        return [
            self::TAG_PROJECT,
            self::TAG_QUESTION,
            self::TAG_HOMEWORK,
        ];
    }

    public function scopeVisible($query)
    {
        return $query->whereNull('hidden_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public static function normalizeTitleString(string $title): string
    {
        $title = preg_replace('/\s+/u', ' ', trim($title));

        return mb_strtolower($title, 'UTF-8');
    }

    public function dedupeKey(): string
    {
        $sectionKey = $this->section_id ? (string) $this->section_id : 'general';

        return $sectionKey.'|'.self::normalizeTitleString((string) $this->title);
    }

    public static function dedupeForDisplay(Collection $threads): Collection
    {
        return $threads
            ->sortByDesc(fn (Thread $thread) => $thread->is_pinned ? 1 : 0)
            ->sortByDesc(fn (Thread $thread) => $thread->created_at?->timestamp ?? 0)
            ->values()
            ->unique(fn (Thread $thread) => $thread->dedupeKey())
            ->values();
    }
}
