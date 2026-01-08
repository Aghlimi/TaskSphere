<?php

namespace Tests\Feature;

use App\Models\Feature;
use App\Models\Member;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private $task;
    private $project;
    private $feature;
    private $member;
    private $admin;
    private $owner;
    private $user;


    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::factory()->create();
        $this->feature = Feature::factory()->create([
            'project_id' => $this->project->id
        ]);
        $this->task = Task::factory()->create([
            'feature_id' => 1
        ]);
        $this->user = User::factory()->create();
        $this->member = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->owner = User::factory()->create();

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

    public function test_create_new_Task_as_user()
    {
        $this->actingAs($this->user);
        $response = $this->postJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks", [
            'title' => 'task title',
            'description' => 'desc'
        ]);
        $response->assertStatus(403);
    }

    public function test_create_new_Task_as_member()
    {
        $this->actingAs($this->member);
        $response = $this->postJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks", [
            'title' => 'task title',
            'description' => 'desc'
        ]);
        $response->assertStatus(403);
    }

    public function test_create_new_Task_as_admin()
    {
        $this->actingAs($this->admin);
        $response = $this->postJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks", [
            'title' => 'task title',
            'description' => 'desc'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'task title']);
    }

    public function test_create_new_Task_as_owner()
    {
        $this->actingAs($this->owner);
        $response = $this->postJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks", [
            'title' => 'task title',
            'description' => 'desc'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'task title']);
    }

    public function test_fetch_all_tasks_as_user()
    {
        $this->actingAs($this->user);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks");
        $response->assertStatus(403);
    }
    public function test_fetch_all_tasks_as_member()
    {
        $this->actingAs($this->member);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks");
        $response->assertStatus(200);
    }
    public function test_fetch_all_tasks_as_owner()
    {
        $this->actingAs($this->owner);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks");
        $response->assertStatus(200);
    }
    public function test_fetch_all_tasks_as_admin()
    {
        $this->actingAs($this->admin);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks");
        $response->assertStatus(200);
    }
    public function test_update_tasks_as_user()
    {
        $this->actingAs($this->user);
        $response = $this->putJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '1'
        ]);
        $response->assertStatus(403);
    }
    public function test_update_tasks_as_member()
    {
        $this->actingAs($this->member);
        $response = $this->putJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '1'
        ]);
        $response->assertStatus(403);
    }

    public function test_update_tasks_as_admin()
    {
        $this->actingAs($this->admin);
        $response = $this->putJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '1'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => '1'
        ]);
    }
    public function test_update_tasks_as_owner()
    {
        $this->actingAs($this->owner);
        $response = $this->putJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '2'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => '2'
        ]);
    }

    public function test_edit_tasks_as_user()
    {
        $this->actingAs($this->user);
        $response = $this->patchJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '1'
        ]);
        $response->assertStatus(403);
    }
    public function test_edit_tasks_as_member()
    {
        $this->actingAs($this->member);
        $response = $this->patchJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '1'
        ]);
        $response->assertStatus(403);
    }

    public function test_edit_tasks_as_admin()
    {
        $this->actingAs($this->admin);
        $response = $this->patchJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '1'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => '1'
        ]);
    }
    public function test_edit_tasks_as_owner()
    {
        $this->actingAs($this->owner);
        $response = $this->patchJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}", [
            'title' => '2'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => '2'
        ]);
    }

    public function test_fetch_single_task_as_user()
    {
        $this->actingAs($this->user);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(403);
    }

    public function test_fetch_single_task_as_member()
    {
        $this->actingAs($this->member);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(200);
    }

    public function test_fetch_single_task_as_admin()
    {
        $this->actingAs($this->admin);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(200);
    }

    public function test_fetch_single_task_as_owner()
    {
        $this->actingAs($this->owner);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(200);
    }

    public function test_delete_tasks_as_user()
    {
        $this->actingAs($this->user);
        $response = $this->deleteJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(403);
    }
    public function test_delete_tasks_as_member()
    {
        $this->actingAs($this->member);
        $response = $this->deleteJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(403);
    }
    public function test_delete_tasks_as_admin()
    {
        $this->actingAs($this->admin);
        $response = $this->deleteJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', [
            'id' => $this->task->id
        ]);
    }

    public function test_delete_tasks_as_owner()
    {
        $this->actingAs($this->owner);
        $response = $this->deleteJson("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', [
            'id' => $this->task->id
        ]);
    }
}
