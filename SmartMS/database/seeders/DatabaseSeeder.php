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

        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => User::ROLE_STUDENT,
                'grade' => 9,
                'placement_passed' => true,
            ]
        );
        if (!$testUser->wasRecentlyCreated) {
            $testUser->update(['name' => 'Test User', 'role' => User::ROLE_STUDENT, 'grade' => 9, 'placement_passed' => true]);
        }

        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Teacher',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => User::ROLE_TEACHER,
            ]
        );
        if (!$teacher->wasRecentlyCreated) {
            $teacher->update(['name' => 'Teacher', 'role' => User::ROLE_TEACHER]);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );
        if (!$admin->wasRecentlyCreated) {
            $admin->update(['name' => 'Admin', 'role' => User::ROLE_ADMIN]);
        }

        $adminGmail = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'role' => User::ROLE_ADMIN,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        if (!$adminGmail->wasRecentlyCreated) {
            $adminGmail->update(['name' => 'Admin', 'role' => User::ROLE_ADMIN]);
        }

        $this->call(AchievementSeeder::class);
        $this->call(InformaticsSeeder::class);
        $this->call(BeginnerLessonsSeeder::class);
        $this->call(ContentSeeder::class);
        $this->call(LessonsQuizzesExtraSeeder::class);
        $this->call(InformaticsByGradeSeeder::class);
        $this->call(Grade11RevisionExpansionSeeder::class);
        $this->call(ExamQuizSeeder::class);
        $this->call(TranslationsKkSeeder::class);
        $this->call(EnsureMinQuizQuestionsSeeder::class);
    }
}
