<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_can_be_created()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/projects', [
            'title' => 'Test Project',
            'description' => 'A test project',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('projects', ['title' => 'Test Project']);
    }

    public function test_project_can_be_fetched()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();
        $response = $this->getJson('/api/projects/' . $project->id);
        $response->assertStatus(403);

        $member = Member::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'role' => 'member',
        ]);
        $response = $this->getJson('/api/projects/' . $project->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $project->id]);
    }

    public function test_project_can_be_updated()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $project = Project::factory()->create();

        $response = $this->putJson('/api/projects/' . $project->id, [
            'title' => 'Updated Project',
        ]);

        $response->assertStatus(403);

        $member = Member::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'role' => 'member',
        ]);

        $response = $this->putJson('/api/projects/' . $project->id, [
            'title' => 'Updated Project',
        ]);

        $response->assertStatus(403);
        $member->role = 'owner';
        $member->save();
        $response = $this->putJson('/api/projects/' . $project->id, [
            'title' => 'Updated Project',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', ['title' => 'Updated Project']);
    }

    public function test_project_can_be_deleted()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $response = $this->deleteJson('/api/projects/' . $project->id);
        $response->assertStatus(403);

        $member = Member::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'role' => 'member',
        ]);
        $response = $this->deleteJson('/api/projects/' . $project->id);
        $response->assertStatus(403);
        $member->role = 'owner';
        $member->save();
        $response = $this->deleteJson('/api/projects/' . $project->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
