# Fix Admin Access Issue

## Problem
Getting "Unauthorized. Admin access required." error when trying to access admin routes.

## Solutions

### Option 1: Run Database Seeder (If not done yet)
If you haven't run the seeder yet, run:
```bash
php artisan db:seed
```

This will create:
- All roles (admin, staff, clerk, inspector, resident)
- Admin user with email: `admin@sumbong.app` and password: `password`
- Sample service types

### Option 2: Assign Admin Role to Existing User
If you already have a user account and want to make it admin:

```bash
php artisan tinker
```

Then run:
```php
$user = App\Models\User::where('email', 'your-email@example.com')->first();
$adminRole = App\Models\Role::where('name', 'admin')->first();
$user->role_id = $adminRole->id;
$user->save();
exit
```

### Option 3: Create Admin User Manually
```bash
php artisan tinker
```

Then run:
```php
$adminRole = App\Models\Role::firstOrCreate(['name' => 'admin'], ['permissions' => ['*']]);
$user = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'user_type' => 'resident',
    'verified' => true,
    'role_id' => $adminRole->id,
]);
exit
```

### Option 4: Check Current User's Role
To check if your current user has admin role:

```bash
php artisan tinker
```

Then run:
```php
$user = App\Models\User::where('email', 'your-email@example.com')->with('role')->first();
echo "User: " . $user->name . "\n";
echo "Role ID: " . $user->role_id . "\n";
echo "Role Name: " . ($user->role ? $user->role->name : 'NULL') . "\n";
echo "Is Admin: " . ($user->isAdmin() ? 'YES' : 'NO') . "\n";
exit
```

## After Fixing
1. Log out and log back in
2. Try accessing `/admin` again
3. You should now have access to the admin dashboard

## Default Admin Credentials (After Seeding)
- Email: `admin@sumbong.app`
- Password: `password`

**⚠️ IMPORTANT: Change these credentials in production!**

