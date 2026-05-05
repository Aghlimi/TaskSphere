<?php

namespace App\Providers;

use App\Models\Member;
use App\Policies\MemberPolicy;
use App\Services\MemberService;
use App\Repositories\MemberRepository;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class MemberProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('MemberService',function(){
            return new MemberService();
        });
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Member::class,MemberPolicy::class);
    }
}
