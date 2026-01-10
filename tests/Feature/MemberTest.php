<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\ExpectationFailedException;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;
    private $member;
    private $user;
    private $user2;
    private $admin;
    private $owner;
    private $roles;
    private $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->member = User::factory()->create();
        $this->user = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->project = Project::factory()->create();

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

        $this->roles = [
            ['role' => $this->admin, 'status' => 200, 'name' => 'admin'],
            ['role' => $this->owner, 'status' => 200, 'name' => 'owner'],
            ['role' => $this->member, 'status' => 200, 'name' => 'member'],
            ['role' => $this->user, 'status' => 200, 'name' => 'user'],
        ];
    }

    private function setStatus(array $status)
    {
        for ($i = 0; $i < count(value: $status); $i++) {
            $this->roles[$i]['status'] = $status[$i];
        }
    }

    public function test_list_members()
    {
        $this->setStatus([200, 200, 200, 403]);
        for ($i = 0; $i < count(value: $this->roles); $i++) {
            $this->actingAs($this->roles[$i]['role']);
            $response = $this->get("/api/projects/{$this->project->id}/members");
            try {
                $response->assertStatus($this->roles[$i]["status"]);
                if ($this->roles[$i]["status"] == 200) {
                    $response->assertJsonIsArray();
                }
            } catch (ExpectationFailedException $e) {
                Log::error("test_list_members/" . $i);
                throw $e;
            }
        }
    }

    public function test_invite_new_member()
    {
        $this->setStatus([200, 200, 403, 403]);
        for ($i = 0; $i < count(value: $this->roles); $i++) {
            $this->actingAs($this->roles[$i]['role']);
            $response = $this->post("/api/projects/{$this->project->id}/members/{$this->user2->id}");
            $response->assertStatus($this->roles[$i]["status"]);
            if ($this->roles[$i]["status"] == 200) {
                $this->assertDatabaseHas('invitations', [
                    'user_id' => $this->user2->id
                ]);
            }
        }
    }

    public function test_accept()
    {
        $inv = Invitation::factory()->forProject($this->project->id)->create([
            'user_id' => $this->user2->id,
            'sender_id' => $this->owner->id
        ]);
        $this->actingAs($this->owner);
        $response = $this->get("/api/projects/{$this->project->id}/members/accept/{$inv->id}");
        $response->assertStatus(403);
        $this->actingAs($this->user2);
        $response = $this->get("/api/projects/{$this->project->id}/members/accept/{$inv->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('invitations', ['id' => $inv->id]);
        $this->assertDatabaseHas('members', ['user_id' => $inv->user_id]);
    }

    public function test_reject()
    {
        $inv = Invitation::factory()->forProject($this->project->id)->create([
            'user_id' => $this->user2->id,
            'sender_id' => $this->owner->id
        ]);

        $this->actingAs($this->owner);
        $response = $this->get("/api/projects/{$this->project->id}/members/reject/{$inv->id}");
        $response->assertStatus(403);
        $this->actingAs($this->user2);
        $response = $this->get("/api/projects/{$this->project->id}/members/reject/{$inv->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('invitations', ['id' => $inv->id]);
        $this->assertDatabaseMissing('members', ['user_id' => $inv->user_id]);
    }

    public function test_delete_member()
    {
        $this->setStatus([204, 204, 204, 403]);
        for ($i = 0; $i < count($this->roles); $i++) {
            $this->actingAs($this->roles[$i]['role']);
            DB::beginTransaction();
            $response = $this->delete("/api/projects/{$this->project->id}/members/delete/{$this->member->id}");
            try {
                $response->assertStatus($this->roles[$i]['status']);
            } catch (ExpectationFailedException $e) {
                Log::error("test_delete_member/" . $this->roles[$i]["name"] . $response->getStatusCode());
                throw $e;
            }
            DB::rollBack();
        }
    }

    public function test_set_admin()
    {
        $this->setStatus([403, 200, 403, 403]);
        for ($i = 0; $i < count($this->roles); $i++) {
            $this->actingAs($this->roles[$i]['role']);
            DB::beginTransaction();
            $response = $this->post("/api/projects/{$this->project->id}/members/setadmin/{$this->member->id}");
            try {
                $response->assertStatus($this->roles[$i]['status']);
            } catch (ExpectationFailedException $e) {
                Log::error("test_delete_member/" . $this->roles[$i]["name"] . $response->getStatusCode());
                throw $e;
            }
            DB::rollBack();
        }
    }

    public function test_remove_Admin()
    {
        $this->setStatus([403, 204, 403, 403]);
        for ($i = 0; $i < count($this->roles); $i++) {
            $this->actingAs($this->roles[$i]['role']);
            DB::beginTransaction();
            $response = $this->delete("/api/projects/{$this->project->id}/members/removeadmin/{$this->admin->id}");
            try {
                $response->assertStatus($this->roles[$i]['status']);
            } catch (ExpectationFailedException $e) {
                Log::error("test_delete_member/" . $this->roles[$i]["name"] . $response->getStatusCode());
                throw $e;
            }
            DB::rollBack();
        }
    }
}
