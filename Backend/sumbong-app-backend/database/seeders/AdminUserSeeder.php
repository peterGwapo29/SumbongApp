<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        if (! $adminRole) {
            $this->command->error('Admin role not found. Run role seeding first.');

            return;
        }

        $email = config('admin.email');
        $password = config('admin.password');

        if (empty($email) || empty($password)) {
            $this->command->warn('ADMIN_EMAIL or ADMIN_PASSWORD is not set. Skipping admin user seed.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrator',
                'mobile' => null,
                'address' => null,
                'avatar_url' => null,
                'user_type' => 'resident',
                'verified' => true,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'role_id' => $adminRole->id,
            ]
        );

        $this->command->info("Admin account created/updated successfully for {$email}.");
    }
}
