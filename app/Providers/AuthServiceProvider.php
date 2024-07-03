<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Contracts\AuthInterface;
use App\Services\AuthService;
use App\Contracts\PostInterface;
use App\Services\PostService;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    ];

    public function register()
    {
        $this->app->bind(AuthInterface::class, AuthService::class);
        $this->app->bind(PostInterface::class, PostService::class);
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
