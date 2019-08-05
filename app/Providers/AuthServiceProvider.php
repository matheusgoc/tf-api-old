<?php

namespace App\Providers;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Image;
use App\Policies\AgentPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ImagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Customer::class => CustomerPolicy::class,
        Agent::class => AgentPolicy::class,
        Image::class => ImagePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
