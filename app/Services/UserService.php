<?php

namespace App\Services;

use App\Events\ErrorLogs;
use App\Exceptions\ResponceException;
use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserService
{
    use AuthorizesRequests;

    public function all(): Collection
    {
        $this->authorize("viewAny", User::class);

        return User::select('email', 'name', 'role')->get();
    }

    public function find(User $user)
    {
        $this->authorize("view", [User::class, $user]);
        return $user;
    }

    public function create(array $data)
    {
        $data["password"] = Hash::make($data["password"]);

        $user = User::create($data);

        event(new UserCreated($user));
        return $user;
    }

    public function login(array $data)
    {
        $user = User::where("email", $data["email"])->first();

        if (!$user || !Hash::check($data["password"], $user->password))
            return null;

        return $user->createToken("auth_token")->plainTextToken;
    }

    public function update(User $user, array $data)
    {
        $this->authorize("update", $user);

        if (isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        }

        $user->update([
            "name" => $data["name"] ?? $user->name,
            "email" => $data["email"] ?? $user->email,
            "password" => $data["password"] ?? $user->password,
        ]);
    }

    public function delete(User $user)
    {
        $this->authorize("delete", $user);
        $user->delete();
    }
}
