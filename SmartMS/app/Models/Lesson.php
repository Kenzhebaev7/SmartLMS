<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'title_kk',
        'content',
        'content_kk',
        'video_url',
        'video_id',
        'file_path',
        'is_advanced',
        'order',
    ];

    public function getTitleForLocale(string $locale): string
    {
        if ($locale === 'kk' && !empty($this->title_kk)) {
            return $this->title_kk;
        }
        return $this->title ?? '';
    }

    public function getContentForLocale(string $locale): ?string
    {
        if ($locale === 'kk' && $this->content_kk !== null && $this->content_kk !== '') {
            return $this->content_kk;
        }
        return $this->content;
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
