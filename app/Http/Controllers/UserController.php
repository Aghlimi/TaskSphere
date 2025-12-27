<?php

namespace App\Http\Controllers;

use App\Exceptions\ResponceException;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        Log::info($request->user());

        try {
            return response()->json($this->userService->all());
        } catch (ResponceException $e) {
            return response()->json(["message" => $e->getMessage()], $e->statusCode);
        }
    }

    public function showLoginForm()
    {
        return view("users.login");
    }

    public function login(Request $request)
    {
        try {
            return $this->userService->login($request);
        } catch (ResponceException $e) {
            return response()->json(["message" => $e->getMessage()], $e->statusCode);
        }
    }

    public function create(Request $request)
    {
        return view("users.create");
    }

    public function store(Request $request)
    {
        try {
            $this->userService->create($request->only("name", "email", "password", "password_confirmation"));
            return response()->json(["message" => "User created successfully"], 201);
        } catch (ResponceException $e) {
            return response()->json(["message" => $e->getMessage()], $e->statusCode);
        }
    }

    public function show(string $id)
    {
        return $this->userService->find((int) $id);
    }

    public function edit(Request $request)
    {
        $this->userService->update($request->user(), $request->all());
        return response()->json([
            "message" => "User updated successfully",
        ]);
    }

    public function update(Request $request)
    {
        $this->userService->update($request->user(), $request->all());
        return response()->json([
            "message" => "User updated successfully",
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::find((int) $id);
        $this->userService->delete($user);
        return response()->json(["message" => "User deleted successfully"], 204);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return redirect('/login');
    }
}
