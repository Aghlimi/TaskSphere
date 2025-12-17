<?php

namespace App\Services;

use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get all users.
     */
    public function all()
    {
        return User::all();
    }

    /**
     * Get a user by ID.
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        event(new UserCreated($user));

        return $user;
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user;
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
