<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Section extends Model
{
    protected $fillable = [
        'title',
        'title_kk',
        'description',
        'description_kk',
        'order',
        'grade',
        'is_revision',
        'is_featured',
        'deadline_at',
    ];

    public function scopeForGrade($query, ?int $grade)
    {
        if ($grade === null) {
            return $query;
        }
        return $query->where('grade', $grade);
    }

    public function scopeRevisionOnly($query)
    {
        return $query->where('is_revision', true);
    }

    public function scopeMainOnly($query)
    {
        return $query->where(function ($q) {
            $q->where('is_revision', false)->orWhereNull('is_revision');
        });
    }

    /** Порядок вывода: избранный раздел (проектная деятельность) всегда первый, затем по полю order. */
    public function scopeOrderedForDisplay($query)
    {
        return $query->orderByDesc('is_featured')->orderBy('order')->orderBy('id');
    }

    /**
     * Ключ для слияния дублей в списке разделов.
     * «Проектная деятельность…» может быть записана с мелкими отличиями в БД — один ключ на класс.
     */
    public function dedupeKey(): string
    {
        $g = $this->grade === null ? '' : (string) (int) $this->grade;

        foreach ([$this->title, $this->title_kk] as $t) {
            $norm = self::normalizeTitleString((string) $t);
            if (self::isProjectActivityTitleNormalized($norm)) {
                return 'project-activity|'.$g;
            }
        }

        return self::normalizeTitleString((string) ($this->title ?? '')).'|'.$g;
    }

    /** Совпадение с разделом «Проектная деятельность на уроках информатики» (любые мелкие варианты формулировки). */
    public static function isProjectActivityTitleNormalized(string $normalizedRuTitle): bool
    {
        if ($normalizedRuTitle === '') {
            return false;
        }

        return str_contains($normalizedRuTitle, 'проектн')
            && str_contains($normalizedRuTitle, 'информатик');
    }

    /** Для порядка карточек: смотрим и title, и title_kk. */
    public static function looksLikeProjectActivitySection(self $section): bool
    {
        foreach ([$section->title, $section->title_kk] as $t) {
            if (self::isProjectActivityTitleNormalized(self::normalizeTitleString((string) $t))) {
                return true;
            }
        }

        return false;
    }

    public static function normalizeTitleString(string $title): string
    {
        $title = preg_replace('/\s+/u', ' ', trim($title));

        return mb_strtolower($title, 'UTF-8');
    }

    /**
     * «Проектная деятельность…» — первая тема в кабинете, даже если в БД order остался в конце.
     */
    public static function withProjectActivityFirst(Collection $sections): Collection
    {
        $projects = $sections->filter(fn (Section $s) => self::looksLikeProjectActivitySection($s))->sortBy([
            fn (Section $s) => (int) ($s->grade ?? 0),
            fn (Section $s) => (int) $s->order,
            fn (Section $s) => $s->id,
        ])->values();

        $rest = $sections->reject(fn (Section $s) => self::looksLikeProjectActivitySection($s))->sortBy([
            fn (Section $s) => $s->is_featured ? 0 : 1,
            fn (Section $s) => $s->order,
            fn (Section $s) => $s->id,
        ])->values();

        return $projects->concat($rest)->values();
    }

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

    protected function casts(): array
    {
        return [
            'grade' => 'integer',
            'is_revision' => 'boolean',
            'is_featured' => 'boolean',
            'deadline_at' => 'datetime',
        ];
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
