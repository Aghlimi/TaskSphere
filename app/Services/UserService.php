<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserService
{
    use AuthorizesRequests;

    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function all(): Collection
    {
        $this->authorize("viewAny", User::class);

        return $this->users->all();
    }

    public function find(User $user)
    {
        $this->authorize("view", [User::class, $user]);
        return $user;
    }

    public function create(array $data)
    {
        $data["password"] = Hash::make($data["password"]);

        $user = $this->users->create($data);

        event(new UserCreated($user));
        return $user;
    }

    public function login(array $data)
    {
        $user = $this->users->findByEmail($data["email"]);

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

        $this->users->update($user, [
            "name" => $data["name"] ?? $user->name,
            "email" => $data["email"] ?? $user->email,
            "password" => $data["password"] ?? $user->password,
        ]);
    }

    public function delete(User $user)
    {
        $this->authorize("delete", $user);
        $this->users->delete($user);
    }
}
