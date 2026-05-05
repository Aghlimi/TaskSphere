<?php

namespace App\Providers;

use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Services\TaskService;
use App\Repositories\TaskRepository;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class TaskProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('TaskService', function () {
            return new TaskService();
        });
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);
    }
}
