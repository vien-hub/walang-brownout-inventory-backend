<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'Administrator', 'status' => 'Active']);
    }

    public function test_connection_check_works(): void
    {
        $this->getJson('/api/test-connection')->assertOk()->assertJson(['status' => 'success']);
    }

    public function test_register_creates_warehouse_staff_and_returns_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Staff One',
            'email' => 'staff@example.com',
            'password' => 'secret123',
        ])->assertCreated()->assertJsonStructure(['token', 'user']);

        $this->assertDatabaseHas('users', ['email' => 'staff@example.com', 'role' => 'Warehouse Staff']);
    }

    public function test_login_rejects_wrong_password_and_inactive_accounts(): void
    {
        $user = User::factory()->create(['email' => 'a@example.com', 'password' => bcrypt('secret123')]);

        $this->postJson('/api/login', ['email' => 'a@example.com', 'password' => 'wrong'])->assertStatus(422);

        $user->update(['status' => 'Inactive']);
        $this->postJson('/api/login', ['email' => 'a@example.com', 'password' => 'secret123'])->assertStatus(403);
    }

    public function test_data_endpoints_need_login_and_only_accept_known_keys(): void
    {
        $this->getJson('/api/data')->assertUnauthorized();

        $this->actingAs($this->admin(), 'sanctum');
        $this->putJson('/api/data/inventory_db', ['value' => '[]'])->assertOk();
        $this->putJson('/api/data/not_a_real_key', ['value' => '[]'])->assertNotFound();
        $this->getJson('/api/data')->assertOk()->assertJsonPath('data.inventory_db', '[]');
    }

    public function test_only_administrators_can_manage_users(): void
    {
        $staff = User::factory()->create(['role' => 'Warehouse Staff', 'status' => 'Active']);
        $this->actingAs($staff, 'sanctum');
        $this->postJson('/api/users', [
            'name' => 'X', 'email' => 'x@example.com', 'password' => 'secret123',
            'role' => 'Warehouse Staff', 'status' => 'Active',
        ])->assertForbidden();
    }
}
