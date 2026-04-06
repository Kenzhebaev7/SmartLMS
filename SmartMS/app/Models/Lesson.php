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
        'grade',
        'title',
        'title_kk',
        'content',
        'content_kk',
        'video_url',
        'video_id',
        'video_url_kk',
        'video_id_kk',
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

    /**
     * Извлекает 11-символьный ID ролика YouTube из URL или из «чистого» id.
     * Поддерживаются watch, embed, shorts, youtu.be, m.youtube.com.
     */
    public static function extractYoutubeVideoId(?string $urlOrId): ?string
    {
        if ($urlOrId === null) {
            return null;
        }
        $s = trim($urlOrId);
        if ($s === '') {
            return null;
        }
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $s)) {
            return $s;
        }
        $patterns = [
            '/(?:youtube\.com\/watch\?(?:[^&]+&)*v=|youtube\.com\/watch\?v=)([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/',
            '/m\.youtube\.com\/watch\?(?:[^&]+&)*v=([a-zA-Z0-9_-]{11})/',
            '/m\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $s, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    /** ID ролика для интерфейса: для KK — отдельное видео, иначе запасной вариант RU. */
    public function youtubeVideoIdForLocale(string $locale): ?string
    {
        if ($locale === 'kk') {
            $id = self::extractYoutubeVideoId($this->video_id_kk ?? null)
                ?? self::extractYoutubeVideoId($this->video_url_kk ?? null);
            if ($id !== null) {
                return $id;
            }
        }

        return self::extractYoutubeVideoId($this->video_id ?? null)
            ?? self::extractYoutubeVideoId($this->video_url ?? null);
    }

    /** URL встраивания (youtube-nocookie). */
    public function youtubeEmbedUrlForLocale(string $locale): ?string
    {
        $id = $this->youtubeVideoIdForLocale($locale);
        if ($id === null) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.$id.'?rel=0&modestbranding=1';
    }

    /** Прямая ссылка на просмотр на YouTube (если известен id). */
    public function youtubeWatchUrlForLocale(string $locale): ?string
    {
        $id = $this->youtubeVideoIdForLocale($locale);

        return $id !== null ? 'https://www.youtube.com/watch?v='.$id : null;
    }

    /** Не YouTube URL для показа внешней ссылки (редко). */
    public function nonYoutubeVideoUrlForLocale(string $locale): ?string
    {
        $url = $locale === 'kk'
            ? ($this->video_url_kk ?: $this->video_url)
            : $this->video_url;
        if ($url === null || trim($url) === '') {
            return null;
        }
        $url = trim($url);
        if (self::extractYoutubeVideoId($url) !== null) {
            return null;
        }
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return null;
    }

    /** Есть ли в данных что-то про видео (для блока «видео»), в т.ч. битая ссылка — показать предупреждение. */
    public function hasVideoDataForLocale(string $locale): bool
    {
        if ($this->youtubeVideoIdForLocale($locale) !== null) {
            return true;
        }
        if ($this->nonYoutubeVideoUrlForLocale($locale) !== null) {
            return true;
        }

        return $this->hasUnparsedVideoFieldsForLocale($locale);
    }

    private function hasUnparsedVideoFieldsForLocale(string $locale): bool
    {
        if ($locale === 'kk') {
            $kkRaw = trim((string) ($this->video_url_kk ?? '')) !== '' || trim((string) ($this->video_id_kk ?? '')) !== '';
            if ($kkRaw) {
                return true;
            }
        }

        return trim((string) ($this->video_url ?? '')) !== '' || trim((string) ($this->video_id ?? '')) !== '';
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
