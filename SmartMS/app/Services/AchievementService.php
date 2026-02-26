<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;

class AchievementService
{
    public function award(User $user, string $achievementKey): bool
    {
        $achievement = Achievement::where('key', $achievementKey)->first();
        if (!$achievement) {
            return false;
        }
        if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
            return false;
        }
        $user->achievements()->attach($achievement->id, ['awarded_at' => now()]);
        $user->increment('xp', $achievement->xp);
        return true;
    }

    public function has(User $user, string $achievementKey): bool
    {
        return $user->achievements()->where('key', $achievementKey)->exists();
    }
}
