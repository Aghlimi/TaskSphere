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
        return $this->memberService->showMembers($project);
    }

    public function invite(Project $project, User $user)
    {
        $this->memberService->invite($project, $user);
        return response()->json(null, 200);
    }

    public function accept(Invitation $inv)
    {
        $this->memberService->accept($inv);
        return response()->json(null, 200);
    }

    public function reject(Invitation $inv)
    {
        $this->memberService->accept($inv);
        return response()->json(null, 200);
    }

    public function delete(Project $project, User $user)
    {
        $this->memberService->delete($project, $user);
        return response()->json(null, 204);
    }
}
