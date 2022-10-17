<?php


namespace Cmsrs\Laracms\Tests\Feature;

use Cmsrs\Laracms\Models\User;

class BaseClean extends  \Orchestra\Testbench\TestCase
{
    protected $token;


    protected function getEnvironmentSetUp($app): void
    {


        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver'   => 'mysql',
            'database' => 'cmsrs_testing_package',
            'prefix'   => '',
            'username' => 'rs',
            'host' => '127.0.0.1',
            'password' => 'secret102*'
        ]);

        $app['config']->set(
            'jwt.secret', 'aaaapisdfsdfsdfsdfert43545364536546346544363453654sdfgsdfg'
        );

        $app['config']->set(
            'auth.default.guard', 'api'
        );

        $app['config']->set(
            'auth.guards.api', [
                'driver' =>  'jwt', //'token',
                'provider' => 'users',
                //'hash' => false,    
            ],
            'auth.guards.web', [
                'driver' =>  'session', //'token',
                'provider' => 'users',
                //'hash' => false,    
            ],            
        );       

        /*
        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => App\User::class,
            ],
        */    

        $app['config']->set(
            'auth.providers.users', [
                'driver' => 'eloquent',
                'model' => User::class
            ]
        );

        $app['config']->set(
            'app.key', 'base64:JjrFWC+TGnySY2LsldPXAxuHpyjh8UuoPMt6yy2gJ8U='
        );


    }

    

    protected function getPackageProviders($app)
    {
        return [
            'Cmsrs\Laracms\Providers\LaracmsProvider',
            'PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider',
            //'Illuminate\Support\Facades\Auth',
            'Illuminate\Auth\AuthServiceProvider'
            
        ];
    }           


    public function setUp(): void
    {
        parent::setUp();

    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }


}
