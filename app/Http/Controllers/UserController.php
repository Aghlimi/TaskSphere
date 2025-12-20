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
    /**
     * Display a listing of the resource.
     */
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
            return response()->json(
                [
                    "message" => $e->getMessage(),
                ],
                $e->statusCode
            );
        }
    }
    public function showLoginForm()
    {
        return view("login");
    }
    public function login(Request $request)
    {
        return $this->userService->login($request);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view("users.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->userService->create(
                $request->only(
                    "name",
                    "email",
                    "password",
                    "password_confirmation",
                ),
            );
            return response()->json(
                [
                    "message" => "User created successfully",
                ],
                201,
            );
        } catch (ResponceException $e) {
            return response()->json(
                [
                    "message" => $e->getMessage(),
                ],
                $e->statusCode
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->userService->find((int) $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $this->userService->update($request->user(), $request->all());
        return response()->json([
            "message" => "User updated successfully",
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->userService->update($request->user(), $request->all());
        return response()->json([
            "message" => "User updated successfully",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find((int) $id);
        $this->userService->delete($user);
        return response()->json(
            [
                "message" => "User deleted successfully",
            ],
            204,
        );
    }
    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            "message" => "Logout successful",
        ]);
    }
}
