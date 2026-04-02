<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionResult extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'question_id',
        'is_correct',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}

