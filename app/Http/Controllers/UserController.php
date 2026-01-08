<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->all();
        return response()->json($users, 200);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $token = $this->userService->login($data);

        return response()->json(['token' => $token], 200);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $this->userService->create($data);

        return response()->json(["message" => "User created successfully"], 201);
    }

    public function show(User $user)
    {
        return $this->userService->find($user);
    }

    public function edit(UserRequest $request)
    {
        $data = $request->validated();

        $this->userService->update($request->user(), $data);

        return response()->json([
            "message" => "User updated successfully",
        ]);
    }

    public function update(UserRequest $request)
    {
        $data = $request->validated();

        $this->userService->update($request->user(), $data);

        return response()->json(["message" => "User updated"]);
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);
        return response()->json(["message" => "User deleted successfully"], 204);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return redirect('/login');
    }
}
