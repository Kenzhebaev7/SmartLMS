<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MakeAdmin1AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin1@gmail.com')->first();
        if (!$user) {
            $this->command->warn('Пользователь admin1@gmail.com не найден. Создайте его или измените email в сидере.');
            return;
        }
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->command->info('Пользователь ' . $user->email . ' теперь имеет роль: ' . $user->role);
    }
}
