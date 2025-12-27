<?php

namespace App\Providers;

use App\Models\Feature;
use App\Policies\FeaturePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class FeatureProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('FeatureService', function ($app) {
            return new \App\Services\FeatureService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Feature::class, FeaturePolicy::class);
    }
}
