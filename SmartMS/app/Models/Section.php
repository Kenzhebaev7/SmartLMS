<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    protected $fillable = ['title', 'title_kk', 'description', 'description_kk', 'order', 'level'];

    public function getTitleForLocale(string $locale): string
    {
        if ($locale === 'kk' && !empty($this->title_kk)) {
            return $this->title_kk;
        }
        return $this->title ?? '';
    }

    public function getDescriptionForLocale(string $locale): ?string
    {
        if ($locale === 'kk' && $this->description_kk !== null && $this->description_kk !== '') {
            return $this->description_kk;
        }
        return $this->description;
    }

    public function scopeForLevel($query, ?string $level)
    {
        if ($level === null) {
            return $query;
        }
        return $query->where(function ($q) use ($level) {
            $q->whereNull('level')->orWhere('level', $level);
        });
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function quiz(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function masters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'section_masters')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }
}
