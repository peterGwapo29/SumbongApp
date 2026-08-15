<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_seeder_assigns_admin_role(): void
    {
        Role::create(['name' => 'admin', 'permissions' => ['*']]);

        config([
            'admin.email' => 'admin@sumbong.app',
            'admin.password' => 'password',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'admin@sumbong.app')->with('role')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
    }

    public function test_api_login_returns_admin_role_and_redirect_url(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'permissions' => ['*']]);

        $admin = User::factory()->create([
            'email' => 'admin@sumbong.app',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.role.name', 'admin')
            ->assertJsonPath('redirect_url', url('/admin'));
    }

    public function test_api_bridge_logs_admin_in_and_redirects_to_dashboard(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'permissions' => ['*']]);

        $admin = User::factory()->create([
            'email' => 'admin@sumbong.app',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->get('/auth/api-bridge?token='.$token);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_api_bridge_rejects_non_admin_users(): void
    {
        $residentRole = Role::create(['name' => 'resident', 'permissions' => []]);

        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'role_id' => $residentRole->id,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->get('/auth/api-bridge?token='.$token);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
