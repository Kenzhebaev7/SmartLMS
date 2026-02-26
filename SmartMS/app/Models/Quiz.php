<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = ['section_id', 'title', 'title_kk', 'passing_percent'];

    public function getTitleForLocale(string $locale): string
    {
        if ($locale === 'kk' && !empty($this->title_kk)) {
            return $this->title_kk;
        }
        return $this->title ?? '';
    }

    protected function casts(): array
    {
        return [
            'passing_percent' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
