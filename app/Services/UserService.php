<?php

namespace App\Services;

use App\Events\ErrorLogs;
use App\Exceptions\ResponceException;
use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class UserService
{
    use AuthorizesRequests;

    public function all(): Collection
    {
        Log::info("Fetching all users");
        $this->authorize("viewAny", User::class);
        Log::info("Authorization successful for viewing all users");
        try {
            return User::all();
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException("users not found", 500);
        }
    }

    public function find(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "user not found",], 404);
        }

        $this->authorize("view", [User::class, $user]);

        try {
            return User::find($id);
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException("user not found", 500);
        }
    }

    public function create(array $data)
    {
        $dataValidated = validator($data, [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
        ])->validate();

        $dataValidated["password"] = Hash::make($dataValidated["password"]);
        try {
            $user = User::create($dataValidated);
            event(new UserCreated($user));
            return $user;
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException("Failed to create user", 422);
        }
    }
    public function login(Request $reuqest)
    {
        $data = $reuqest->only("email", "password");

        $data = validator($data, [
            "email" => "required|email",
            "password" => "required",
        ])->validate();

        $user = User::where("email", $data["email"])->first();

        if (!$user || !Hash::check($data["password"], $user->password)) {
            return response()->json([
                "message" => "The provided credentials are incorrect.",
            ], 401);
        }

        Auth::login($user);

        // $reuqest->session()->regenerate();

        return response()->json([
            "message" => "Login successful",
        ]);
    }

    public function update(User $user, array $data)
    {
        $this->authorize("update", $user);
        $datavalidated = validator($data, [
            "name" => "sometimes|string|max:255",
            "email" =>
                "sometimes|string|email|max:255|unique:users,email," .
                $user->id,
            "password" => "sometimes|string|min:8|confirmed",
        ])->validate();

        if (isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        }

        try {
            $user->update([
                "name" => $data["name"] ?? $user->name,
                "email" => $data["email"] ?? $user->email,
                "password" => $data["password"] ?? $user->password,
            ]);
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException("Failed to update user", 500);
        }
    }

    public function delete(User $user)
    {
        $this->authorize("delete", $user);
        try {
            $user->delete();
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException("Failed to delete user", 500);
        }
    }
}
