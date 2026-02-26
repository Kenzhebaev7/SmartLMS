<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'quiz_id', 'text', 'text_kk', 'type', 'options', 'options_kk', 'correct_answer', 'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'options_kk' => 'array',
            'correct_answer' => 'array',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function getTextForLocale(string $locale): string
    {
        if ($locale === 'kk' && !empty($this->text_kk)) {
            return $this->text_kk;
        }
        return $this->text ?? '';
    }

    public function getOptionsForLocale(string $locale): array
    {
        if ($locale === 'kk' && !empty($this->options_kk)) {
            return $this->options_kk;
        }
        return $this->options ?? [];
    }
}
