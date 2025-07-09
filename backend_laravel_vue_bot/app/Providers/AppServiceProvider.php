<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function($user, $permiso){

            // print_r($permiso);
            if($user->permisos()->contains('manage_all')){
                return true;
            }
            
            return $user->permisos()->contains($permiso); // preguntar si el usuario ($user) contiene el permiso ($permiso)

        });
    }
}
