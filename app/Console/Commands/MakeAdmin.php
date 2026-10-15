<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MakeAdmin extends Command
{
    protected $signature = 'aurevia:make-admin
                            {--name= : Display name}
                            {--email= : Email address}
                            {--password= : Password (omit to be prompted securely)}
                            {--role=super_admin : Role: super_admin, manager or support}';

    protected $description = 'Create or update an administrator account (no public registration exists).';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email');
        $role = $this->option('role');

        if (! array_key_exists($role, User::ROLES)) {
            $this->error('Invalid role. Use: super_admin, manager or support.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Password (min 8 characters)');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:160'],
                'password' => ['required', Password::defaults()],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => $role,
                'is_active' => true,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $this->info(($user->wasRecentlyCreated ? 'Created' : 'Updated')." {$user->roleLabel()} account: {$user->email}");

        return self::SUCCESS;
    }
}
