<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Log::info('UserPolicy@viewAny called for user id: ' . $user->id);
        // return true;
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return !($user->role !== 'admin' && $model->role === 'admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || ($user->role === 'admin' && $model->role !== 'admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id || ($user->role === 'admin' && $model->role !== 'admin');
    }

    /**
     * Determine whether the user can restore the model.`
     */
    public function restore(User $user, User $model): bool
    {
        return $user->id === $model->id || ($user->role === 'admin' && $model->role !== 'admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
