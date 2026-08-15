<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found.');
            return;
        }

        User::updateOrCreate(
            [
                'email' => env('ADMIN_EMAIL'),
            ],
            [
                'name' => 'Administrator',
                'mobile' => null,
                'address' => null,
                'avatar_url' => null,
                'user_type' => 'resident',
                'verified' => true,
                'email_verified_at' => now(),
                'password' => Hash::make(env('ADMIN_PASSWORD')),
                'role_id' => $adminRole->id,
            ]
        );

        $this->command->info('Admin account created/updated successfully.');
    }
}