<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addHours(1));
        Passport::personalAccessTokensExpireIn(now()->addHours(12));

        // Mandatory to define Scope
        Passport::tokensCan([
            'admin' => 'Add/Edit/Delete Users',
            'agente' => 'Add/Edit Users',
            'anonimo' => 'List Users'
        ],
        [
            'admin' => 'Add/Edit/Delete Ceos',
            'agente' => 'Add/Edit Ceos',
            'anonimo' => 'List Ceos'
        ]
    );

        //default scope
        Passport::setDefaultScope([
            'basic'
        ]);
        
    }
}
