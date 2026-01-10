<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Project;
use App\Models\User;
use App\Services\MemberService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(public MemberService $memberService)
    {
    }

    public function listMembers(Project $project)
    {
        $members = $this->memberService->showMembers($project);
        return response()->json($members,200);
    }

    public function invite(Project $project, User $user)
    {
        $this->memberService->invite($project, $user);
        return response()->json(null, 200);
    }

    public function accept($project,Invitation $inv)
    {
        $this->memberService->accept($inv);
        return response()->json(null, 200);
    }

    public function reject($project,Invitation $inv)
    {
        $this->memberService->reject($inv);
        return response()->json(null, 200);
    }

    public function delete(Project $project, User $user)
    {
        $this->memberService->delete($project, $user);
        return response()->json(null, 204);
    }

    public function setAdmin(Project $project, User $user)
    {
        $this->memberService->setAdmin($project, $user);
        return response()->json(null, 200);
    }

    public function removeAdmin(Project $project, User $user)
    {
        if ($this->memberService->removeAdmin($project, $user))
            return response()->noContent();
        return response()->json(['message' => 'you can\'t delete the owner'], 403);
    }

    public function UserRole(Project $project, User $user)
    {
        $this->memberService->userRole($project, $user);
    }
}
