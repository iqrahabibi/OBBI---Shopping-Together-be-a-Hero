<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
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

        Passport::tokensExpireIn(Carbon::now('Asia/Jakarta')
                ->addDays(1));
        Passport::refreshTokensExpireIn(Carbon::now('Asia/Jakarta')
                ->addDays(2));

        $this->registerRolePolicies();
    }

    private function registerRolePolicies()
    {
        Gate::define('super-and-admin-access', function ($user) { // Level User
            return $user->hasRole('Super Admin') || $user->hasRole('Administrator'); // Permission
        });
        Gate::define('super-access', function ($user) { // Level User
            return $user->hasRole('Super Admin'); // Permission
        });
        Gate::define('admin-access', function ($user) { // Level User
            return $user->hasRole('Administrator') || $user->hasRole('Admin Herobi OPF'); // Permission
        });
        Gate::define('herobi-access', function ($user) {
            return $user->hasRole('Herobi');
        });
        Gate::define('omerchant-owner-access', function ($user) {
            return $user->hasRole('OMerchant Owner');
        });
        Gate::define('omerchant-admin-access', function ($user) {
            return $user->hasRole('OMerchant Admin');
        });
        Gate::define('admin-gudang-access', function ($user) {
            return $user->hasRole('Admin Gudang') || $user->hasRole('Administrator');
        });
        Gate::define('admin-herobi-opf', function ($user) {
            return $user->hasRole('Admin Herobi OPF') || $user->hasRole('Administrator');
        });
        Gate::define('purchasing', function ($user) {
            return $user->hasRole('Purchasing') || $user->hasRole('Administrator');
        });
    }
}
