<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['name' => 'resident', 'permissions' => ['view_own_requests', 'create_requests', 'view_own_profile']],
            ['name' => 'staff', 'permissions' => ['view_all_requests', 'assign_requests', 'update_request_status']],
            ['name' => 'admin', 'permissions' => ['*']],
            ['name' => 'clerk', 'permissions' => ['view_all_requests', 'create_notifications']],
            ['name' => 'inspector', 'permissions' => ['view_all_requests', 'update_request_status', 'create_feedback']],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['permissions' => $role['permissions']]
            );
        }

        // Create Service Types
        $serviceTypes = [
            [
                'name' => 'Garbage Pickup',
                'description' => 'Request for garbage collection',
                'department' => 'Sanitation',
                'icon' => '🗑️',
                'is_active' => true,
            ],
            [
                'name' => 'Streetlight Repair',
                'description' => 'Report broken or malfunctioning streetlights',
                'department' => 'Public Works',
                'icon' => '💡',
                'is_active' => true,
            ],
            [
                'name' => 'Building Permit',
                'description' => 'Apply for building construction permit',
                'department' => 'Engineering',
                'icon' => '🏗️',
                'is_active' => true,
            ],
            [
                'name' => 'Business Permit',
                'description' => 'Apply for business operation permit',
                'department' => 'Business Licensing',
                'icon' => '🏪',
                'is_active' => true,
            ],
            [
                'name' => 'Pothole Repair',
                'description' => 'Report potholes on roads',
                'department' => 'Public Works',
                'icon' => '🛣️',
                'is_active' => true,
            ],
            [
                'name' => 'Drainage Issue',
                'description' => 'Report drainage problems',
                'department' => 'Public Works',
                'icon' => '🌊',
                'is_active' => true,
            ],
        ];

        foreach ($serviceTypes as $serviceType) {
            ServiceType::firstOrCreate(
                ['name' => $serviceType['name']],
                $serviceType
            );
        }

        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}
