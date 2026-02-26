<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    protected $signature = 'smartlms:make-admin {email : Email пользователя}';

    protected $description = 'Назначить пользователя администратором по email';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Пользователь с email {$email} не найден.");
            return self::FAILURE;
        }

        if ($user->isAdmin()) {
            $this->info("Пользователь {$email} уже администратор.");
            return self::SUCCESS;
        }

        $user->update(['role' => User::ROLE_ADMIN]);
        $this->info("Пользователь {$email} назначен администратором.");

        return self::SUCCESS;
    }
}
