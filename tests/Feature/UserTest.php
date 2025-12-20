<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_user_service_is_create_user(): void
    {
        $response = $this->postJson('/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
        $response = $this->postJson('/users', [
            'email' => 'Test User',
            'name' => 'rfeed',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(422);
    }

    public function test_user_service_is_login_user(): void
    {
        $response = $this->postJson('/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'testuser@example.com',
            'password' => 'password'
        ]);

        $response->assertJson([
            'message' => 'Login successful',
        ]);
        $response->assertStatus(200);
        /// try logout
        $response = $this->getJson('/logout');
        $response->assertStatus(200);
    }
    public function test_user_service_is_fetch_users()
    {
        $response = $this->postJson('/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(201);

        $response = $this->getJson('/users');
        $response->assertStatus(403);
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'adminuser@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $response = $this->getJson('/users');
        $response->assertStatus(200);

        $this->getJson('/users/' . $user->id);
        $response->assertStatus(200);

        $user->role = 'user';
        $user->save();
        $this->actingAs($user);
        $response = $this->getJson('/users');
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
        $user ->save();
        $this->actingAs($user);
        $response = $this->putJson('/users/' . $user->id, [
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
        $response = $this->deleteJson('/users/' . $user2->id);
        $response->assertStatus(403);
        $response = $this->deleteJson('/users/' . $admin->id);
        $response->assertStatus(403);

        $this->actingAs($admin);
        $response = $this->deleteJson('/users/' . $user->id);
        $response->assertStatus(204);
    }
}
