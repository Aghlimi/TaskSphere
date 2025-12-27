<?php

namespace App\Providers;

use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ProjectProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('ProjectService',function($app){
            return new ProjectService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Project::class,ProjectPolicy::class);
    }
}
