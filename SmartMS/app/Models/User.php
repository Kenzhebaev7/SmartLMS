<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    public const ROLE_STUDENT = 'student';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_ADMIN = 'admin';

    public const GRADE_9 = 9;
    public const GRADE_10 = 10;
    public const GRADE_11 = 11;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'grade',
        'placement_passed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'placement_passed' => 'boolean',
        ];
    }

    public function lessonProgresses(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function sectionMasters(): HasMany
    {
        return $this->hasMany(SectionMaster::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'user_id')->latest('awarded_at')->latest();
    }

    public function issuedCertificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'teacher_id')->latest();
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function placementLevelKey(): ?string
    {
        if ($this->role !== self::ROLE_STUDENT || $this->placement_passed === null) {
            return null;
        }

        return $this->placement_passed ? 'advanced' : 'beginner';
    }

    public function isPlacementBeginner(): bool
    {
        return $this->placementLevelKey() === 'beginner';
    }

    public function isPlacementAdvanced(): bool
    {
        return $this->placementLevelKey() === 'advanced';
    }

    /**
     * Класс для выборки разделов/уроков курса. У ученика без grade в профиле — из конфига.
     */
    public function effectiveGradeForStudent(): ?int
    {
        if ($this->role !== self::ROLE_STUDENT) {
            return null;
        }

        return (int) ($this->grade ?? config('smartlms.default_student_grade', 9));
    }

    /** Список ролей для валидации и выбора в админке */
    public static function roles(): array
    {
        return [self::ROLE_STUDENT, self::ROLE_TEACHER, self::ROLE_ADMIN];
    }
}
