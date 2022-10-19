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
        //$router = $this->app->make(Router::class);
        //dd($router);
        //dd(\Tymon\JWTAuth\Http\Middleware\Authenticate::class);
        //$router->aliasMiddleware('tymon.jwt.auth', Authenticate::class);
        //$router->aliasMiddleware('jwt.auth', GetUserFromToken::class);
        //$router->aliasMiddleware('tymon.jwt.refresh', \Tymon\JWTAuth\Middleware\RefreshToken::class);

        //
        //$this->loadMigrationsFrom(__DIR__ . '/../../src/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        //dd(__DIR__ . '/../database/migrations');
        //$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');        
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');        
        //dd('____________+++++++++++__');
        
        //$router->aliasMiddleware('tymon.jwt.auth', Authenticate::class);
        //$router->aliasMiddleware('jwt.auth', Authenticate::class);

        //die('dziala');
        //$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'laracms');


        $this->publishes([
            __DIR__.'/../public' => public_path('.'),
        ], 'public');


        //(new User)->addAdmin();

        
        $this->publishes([
            __DIR__.'/../database/seeds/' => database_path('seeds')
        ], 'seeds');        

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');        
        
    }

    public function register()
    {
        //dd(__DIR__.'/../routes/api.php');
        //$this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        //dd('_____dd___'.__DIR__.'/../../config/config.php');
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'cmsrs');
    }

}
