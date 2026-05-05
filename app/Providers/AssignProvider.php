<?php

namespace App\Providers;

use App\Models\Assign;
use App\Policies\AssignPolicy;
use App\Services\AssignService;
use App\Repositories\AssignRepository;
use App\Repositories\Contracts\AssignRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AssignProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('AssignService', function () {
            return new AssignService();
        });
        $this->app->bind(AssignRepositoryInterface::class, AssignRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(AssignPolicy::class, Assign::class);
    }
}
