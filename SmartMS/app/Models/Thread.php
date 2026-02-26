<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    protected $fillable = ['user_id', 'section_id', 'title', 'body', 'lesson_id', 'hidden_at'];

    protected function casts(): array
    {
        return ['hidden_at' => 'datetime'];
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
}
