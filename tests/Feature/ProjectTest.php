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

    private $user;
    private $member;
    private $admin;
    private $owner;
    private $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->member = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->owner = User::factory()->create();
        $this->project = Project::factory()->create();
        Member::factory()->create([
            'user_id' => $this->member->id,
            'project_id' => $this->project->id,
            'role' => 'member'
        ]);
        Member::factory()->create([
            'user_id' => $this->admin->id,
            'project_id' => $this->project->id,
            'role' => 'admin'
        ]);
        Member::factory()->create([
            'user_id' => $this->owner->id,
            'project_id' => $this->project->id,
            'role' => 'owner'
        ]);
    }

    public function test_project_can_be_created()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/projects', [
            'title' => 'Test Project',
            'description' => 'A test project',
            'start_date' => now()->toDateString(),
            'completed_at' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('projects', ['title' => 'Test Project']);
        $this->assertDatabaseHas('members', [
            'user_id' => $user->id
        ]);
    }

    public function test_project_can_be_fetched_as_user()
    {
        $this->actingAs($this->user);


        $response = $this->get('/api/projects/' . $this->project->id);
        $response->assertStatus(403);
    }

    public function test_project_can_be_fetched_as_member()
    {
        $this->actingAs($this->member);


        $response = $this->get('/api/projects/' . $this->project->id);
        $response->assertStatus(200);
    }

    public function test_project_can_be_fetched_as_admin()
    {
        $this->actingAs($this->admin);


        $response = $this->get('/api/projects/' . $this->project->id);
        $response->assertStatus(200);
    }

    public function test_project_can_be_fetched_as_owner()
    {
        $this->actingAs($this->owner);


        $response = $this->get('/api/projects/' . $this->project->id);
        $response->assertStatus(200);
    }

    public function test_project_can_be_fetched_all()
    {
        $this->actingAs($this->user);


        $response = $this->get('/api/projects');
        $response->assertStatus(200);
    }



    public function test_project_can_be_updated_as_user()
    {
        $this->actingAs($this->user);
        $res = $this->putJson("/api/projects/{$this->project->id}", [
            'title' => 'new title'
        ]);

        $res->assertStatus(403);
        $this->assertDatabaseMissing('projects', [
            'title' => 'new title'
        ]);
    }

    public function test_project_can_be_updated_as_member()
    {
        $this->actingAs($this->member);
        $res = $this->putJson("/api/projects/{$this->project->id}", [
            'title' => 'new title'
        ]);

        $res->assertStatus(403);
        $this->assertDatabaseMissing('projects', [
            'title' => 'new title'
        ]);
    }

    public function test_project_can_be_updated_as_admin()
    {
        $this->actingAs($this->admin);
        $res = $this->putJson("/api/projects/{$this->project->id}", [
            'title' => 'new title'
        ]);

        $res->assertStatus(403);
        $this->assertDatabaseMissing('projects', [
            'title' => 'new title'
        ]);
    }

    public function test_project_can_be_updated_as_owner()
    {
        $this->actingAs($this->owner);
        $res = $this->putJson("/api/projects/{$this->project->id}", [
            'title' => 'new title'
        ]);

        $res->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'title' => 'new title'
        ]);
    }

    public function test_project_can_be_deleted_as_user()
    {
        $this->actingAs($this->user);
        $res = $this->delete("/api/projects/{$this->project->id}");
        $res->assertStatus(403);
        $this->assertDatabaseHas('projects', ['id' => $this->project->id]);
    }

        public function test_project_can_be_deleted_as_member()
    {
        $this->actingAs($this->member);
        $res = $this->delete("/api/projects/{$this->project->id}");
        $res->assertStatus(403);
        $this->assertDatabaseHas('projects', ['id' => $this->project->id]);
    }

        public function test_project_can_be_deleted_as_admin()
    {
        $this->actingAs($this->admin);
        $res = $this->delete("/api/projects/{$this->project->id}");
        $res->assertStatus(403);
        $this->assertDatabaseHas('projects', ['id' => $this->project->id]);
    }

        public function test_project_can_be_deleted_as_owner()
    {
        $this->actingAs($this->owner);
        $res = $this->delete("/api/projects/{$this->project->id}");
        $res->assertStatus(204);
        $this->assertDatabaseMissing('projects', ['id' => $this->project->id]);
    }
}
