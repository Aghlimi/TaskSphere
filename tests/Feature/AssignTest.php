<?php

namespace Tests\Feature;

use App\Models\Assign;
use App\Models\Feature;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\ExpectationFailedException;
use Tests\TestCase;

class AssignTest extends TestCase
{
    use RefreshDatabase;
    private $user;
    private $owner;
    private $admin;
    private $member;
    private $roles;
    private $project;
    private $feature;
    private $task;

    public function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->member = User::factory()->create();
        $this->user = User::factory()->create();

        $this->roles = [
            ['role' => $this->owner, 'status' => 200, 'name' => 'owner'],
            ['role' => $this->admin, 'status' => 200, 'name' => 'admin'],
            ['role' => $this->member, 'status' => 200, 'name' => 'member'],
            ['role' => $this->user, 'status' => 200, 'name' => 'user'],
        ];

        $this->project = Project::factory()->create();
        $this->feature = Feature::factory()->create(['project_id' => $this->project->id]);
        $this->task = Task::factory()->create(['feature_id' => $this->feature->id]);

        Member::factory()->create([
            "user_id" => $this->owner->id,
            "project_id" => $this->project->id,
            'role' => 'owner'
        ]);

        Member::factory()->create([
            "user_id" => $this->admin->id,
            "project_id" => $this->project->id,
            'role' => 'admin'
        ]);

        Member::factory()->create([
            "user_id" => $this->member->id,
            "project_id" => $this->project->id,
            'role' => 'member'
        ]);
    }

    private function setStatus(array $status)
    {
        for ($i = 0; $i < count(value: $status); $i++) {
            $this->roles[$i]['status'] = $status[$i];
        }
    }

    public function test_list_assignees()
    {
        $this->setStatus([200, 200, 200, 403]);
        for ($i = 0; $i < count(value: $this->roles); $i++) {
            $this->actingAs($this->roles[$i]['role']);
            $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}/assign/");
            try {
                $response->assertStatus($this->roles[$i]["status"]);
                if ($this->roles[$i]["status"] == 200)
                    $response->assertJsonIsArray();
            } catch (ExpectationFailedException $e) {
                Log::info($this->roles[$i]["name"]);
                throw $e;
            }
        }
    }

    public function test_assign()
    {
        $this->setStatus([200, 200, 403, 403]);
        for ($i = 0; $i < count(value: $this->roles); $i++) {
            DB::beginTransaction();
            $this->actingAs($this->roles[$i]['role']);
            $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}/assign/{$this->member->id}");
            try {
                $response->assertStatus($this->roles[$i]["status"]);
            } catch (ExpectationFailedException $e) {
                Log::info($this->roles[$i]["name"]);
                throw $e;
            }
            Db::rollBack();
        }
    }

    public function test_accept()
    {
        $inv = Invitation::factory()->forTask($this->task->id)->create([
            'user_id' => $this->member->id,
            'sender_id' => $this->owner->id
        ]);
        $this->actingAs($this->member);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}/assign/accept/{$inv->id}");
        $response->assertStatus(200);
        $this->assertDatabaseHas('assigns', [
            'user_id' => $this->member->id,
            'task_id' => $this->task->id
        ]);
        $this->assertDatabaseMissing('invitations', [
            'user_id' => $this->member->id
        ]);
    }

    public function test_reject()
    {
        $inv = Invitation::factory()->forTask($this->task->id)->create([
            'user_id' => $this->member->id,
            'sender_id' => $this->owner->id
        ]);
        $this->actingAs($this->member);
        $response = $this->get("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}/assign/reject/{$inv->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('assigns', [
            'user_id' => $this->member->id,
            'task_id' => $this->task->id
        ]);
        $this->assertDatabaseMissing('invitations', [
            'user_id' => $this->member->id
        ]);
    }

    public function test_delete()
    {
        $assign = Assign::factory()->create([
            'user_id' => $this->member->id,
            'task_id' => $this->task->id
        ]);
        $this->setStatus([204, 204, 204, 403]);
        for ($i = 0; $i < count(value: $this->roles); $i++) {
            DB::beginTransaction();
            $this->actingAs($this->roles[$i]['role']);
            $response = $this->delete("/api/projects/{$this->project->id}/features/{$this->feature->id}/tasks/{$this->task->id}/assign/{$this->member->id}");
            try {
                $response->assertStatus($this->roles[$i]["status"]);
                if ($this->roles[$i]["status"] == 204)
                    $this->assertDatabaseMissing("assigns", ['id' => $assign->id]);
            } catch (ExpectationFailedException $e) {
                Log::info($this->roles[$i]["name"]);
                throw $e;
            }
            DB::rollBack();
        }
    }
}
