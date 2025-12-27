<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_user_service_create_valid_user(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function test_user_service_create_invalid_user(): void
    {
        $response = $this->postJson('/api/users', [
            'email' => 'Test User',
            'name' => 'rfeed',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(422);
    }

    public function test_user_service_is_login_user(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password'
        ]);

        $response->assertJson([
            'message' => 'Login successful',
            'token' => true,
        ]);

        $response->assertStatus(200);
    }

    public function test_user_service_is_fetch_users_with_user(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(201);

        $response = $this->getJson('/api/users');
        $response->assertStatus(403);
    }

    public function test_user_service_is_fetch_users_with_admin_role(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'adminuser@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
    }

    public function test_user_service_is_fetch_single_user()
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'adminuser@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);


        $response = $this->getJson('/api/users/1');
        $response->assertStatus(200);
    }

    public function test_user_service_is_fetch_users_with_user_role()
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'adminuser@example.com',
            'role' => 'user',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $response = $this->getJson('/api/users');
        $response->assertStatus(403);
    }

    public function test_user_service_is_update_user()
    {
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'regularuser@example.com',
            'role' => 'user',
            'password' => bcrypt('password'),
        ]);
        $user->save();
        $this->actingAs($user);
        $response = $this->putJson('/api/users', [
            'name' => 'Updated User',
            'email' => 'updateduser@example.com',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'updateduser@example.com',
        ]);
    }

    public function test_user_service_is_delete_user()
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'adminuser@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'regularuser@example.com',
            'role' => 'user',
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'name' => 'Regular User2',
            'email' => 'regularuser2@example.com',
            'role' => 'user',
            'password' => bcrypt('password'),
        ]);


        $this->actingAs($user);
        $response = $this->deleteJson('/api/users/' . $user2->id);
        $response->assertStatus(403);
        $response = $this->deleteJson('/api/users/' . $admin->id);
        $response->assertStatus(403);

        $this->actingAs($admin);
        $response = $this->deleteJson('/api/users/' . $user->id);
        $response->assertStatus(204);
    }
}
