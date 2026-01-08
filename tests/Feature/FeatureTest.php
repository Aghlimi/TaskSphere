<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Feature;
use App\Models\Project;
use App\Models\User;
use App\Models\Member;

class FeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->project = Project::factory()->create();
        Member::factory()->create([
            'user_id' => $this->admin->id,
            'project_id' => $this->project->id,
            'role' => 'admin',
        ]);
        Member::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'role' => 'member',
        ]);
    }

    public function test_admin_can_list_features()
    {
        $this->actingAs($this->admin);
        $response = $this->getJson("/api/projects/{$this->project->id}/features");
        $response->assertStatus(200);
    }

    public function test_member_can_list_features()
    {
        $this->actingAs($this->user);
        $response = $this->getJson("/api/projects/{$this->project->id}/features");
        $response->assertStatus(200);
    }

    public function test_non_member_cannot_list_features()
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        $response = $this->getJson("/api/projects/{$this->project->id}/features");
        $response->assertStatus(403);
    }

    public function test_admin_can_create_feature()
    {
        $this->actingAs($this->admin);
        $data = [
            'title' => 'New Feature',
            'details' => 'Feature details',
            'project_id' => $this->project->id,
        ];
        $response = $this->postJson("/api/projects/{$this->project->id}/features", $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('features', ['title' => 'New Feature']);
    }

    public function test_member_cannot_create_feature()
    {
        $this->actingAs($this->user);
        $data = [
            'title' => 'Member Feature',
            'details' => 'Should not be allowed',
            'project_id' => $this->project->id,
        ];
        $response = $this->postJson("/api/projects/{$this->project->id}/features", $data);
        $response->assertStatus(403);
    }

    public function test_admin_can_update_feature()
    {
        $this->actingAs($this->admin);
        $feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $data = ['title' => 'Updated Title'];
        $response = $this->putJson("/api/projects/{$this->project->id}/features/{$feature->id}", $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('features', ['id' => $feature->id, 'title' => 'Updated Title']);
    }

    public function test_member_cannot_update_feature()
    {
        $this->actingAs($this->user);
        $feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $data = ['title' => 'Should Not Update'];
        $response = $this->putJson("/api/projects/{$this->project->id}/features/{$feature->id}", $data);
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_feature()
    {
        $this->actingAs($this->admin);
        $feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $response = $this->deleteJson("/api/projects/{$this->project->id}/features/{$feature->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('features', ['id' => $feature->id]);
    }

    public function test_member_cannot_delete_feature()
    {
        $this->actingAs($this->user);
        $feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $response = $this->deleteJson("/api/projects/{$this->project->id}/features/{$feature->id}");
        $response->assertStatus(403);
    }

    public function test_admin_and_member_can_view_feature()
    {
        $feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $this->actingAs($this->admin);
        $response = $this->getJson("/api/projects/{$this->project->id}/features/{$feature->id}");
        $response->assertStatus(200);
        $this->actingAs($this->user);
        $response = $this->getJson("/api/projects/{$this->project->id}/features/{$feature->id}");
        $response->assertStatus(200);
    }

    public function test_non_member_cannot_view_feature()
    {
        $feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        $response = $this->getJson("/api/projects/{$this->project->id}/features/{$feature->id}");
        $response->assertStatus(403);
    }

    public function test_validation_fails_when_creating_feature_with_missing_title()
    {
        $this->actingAs($this->admin);
        $data = [
            'details' => 'No title',
            'project_id' => $this->project->id,
        ];
        $response = $this->postJson("/api/projects/{$this->project->id}/features", $data);
        $response->assertStatus(422);
    }

    public function test_cannot_update_feature_with_no_data()
    {
        $this->actingAs($this->admin);
        $feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $response = $this->putJson("/api/projects/{$this->project->id}/features/{$feature->id}", []);
        $response->assertStatus(400);
    }
}
