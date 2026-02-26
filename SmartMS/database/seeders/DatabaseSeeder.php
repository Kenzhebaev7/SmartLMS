<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => User::ROLE_STUDENT,
            'level' => 'beginner',
        ]);

        User::factory()->create([
            'name' => 'Teacher',
            'email' => 'teacher@example.com',
            'role' => User::ROLE_TEACHER,
            'level' => 'advanced',
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
            'level' => null,
        ]);

        $adminGmail = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'role' => User::ROLE_ADMIN,
                'level' => null,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        if (!$adminGmail->wasRecentlyCreated) {
            $adminGmail->update(['name' => 'Admin', 'role' => User::ROLE_ADMIN, 'level' => null]);
        }

        $this->call(AchievementSeeder::class);
        $this->call(InformaticsSeeder::class);
        $this->call(BeginnerLessonsSeeder::class);
        $this->call(ContentSeeder::class);
        $this->call(LessonsQuizzesExtraSeeder::class);
        $this->call(TranslationsKkSeeder::class);
    }
}
