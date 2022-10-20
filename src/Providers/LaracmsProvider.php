<?php


namespace Cmsrs\Laracms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

//use Cmsrs\Laracms\Models\User;

class LaracmsProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');        
        $this->loadViewsFrom(__DIR__.'/../views', 'laracms');


        $this->publishes([
            __DIR__.'/../public' => public_path('.'),
        ], 'public');


        //(new User)->addAdmin();

        
        $this->publishes([
            __DIR__.'/../database/seeds/' => database_path('seeders')
        ], 'seeders');        

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');        
        
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'cmsrs');
    }

}
