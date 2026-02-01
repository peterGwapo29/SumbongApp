<?php

// Quick script to assign admin role to a user
// Usage: php assign-admin-role.php your-email@example.com

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = $argv[1] ?? null;

if (!$email) {
    echo "Usage: php assign-admin-role.php your-email@example.com\n";
    exit(1);
}

$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "User with email {$email} not found.\n";
    exit(1);
}

$adminRole = \App\Models\Role::where('name', 'admin')->first();

if (!$adminRole) {
    echo "Admin role not found. Please run: php artisan db:seed\n";
    exit(1);
}

$user->role_id = $adminRole->id;
$user->save();

echo "Admin role assigned to {$user->name} ({$user->email})\n";
echo "You can now log in and access the admin panel.\n";

