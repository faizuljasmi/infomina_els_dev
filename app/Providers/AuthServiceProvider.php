<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;
use App\LeaveApplication;
use App\Policies\LeaveApplicationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        LeaveApplication::class => LeaveApplicationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

         //
        Gate::define('admin-dashboard', function($user){
            return $user->user_type == 'Admin' || $user->user_type == 'Management' || $user->user_type == 'Authority';
        });

        Gate::define('authority-dashboard', function($user){
            return  $user->user_type == 'Authority';
        });

        Gate::define('management-dashboard', function($user){
            return  $user->user_type == 'Management' || $user->user_type == 'Admin';
        });

        Gate::define('employee-data', function($user){
            return $user->user_type == 'Admin' || $user->user_type == 'Management';
        });

        Gate::define('employee-dashboard', function($user){
            return $user->user_type == 'Employee' || $user->user_type == 'Authority';
        });

        Gate::define('edit_users', function($user){
            return $user->user_type == 'Admin';
        });

        Gate::define('edit_settings', function($user){
            return $user->user_type == 'Admin';
        });
    }
}
