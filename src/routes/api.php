<?php

use Illuminate\Http\Request;
use Cmsrs\Laracms\Controllers\AuthController;
use Cmsrs\Laracms\Controllers\ContactController;
use Cmsrs\Laracms\Controllers\ConfigController;
use Cmsrs\Laracms\Controllers\PageController;
use Cmsrs\Laracms\Controllers\MenuController;


use Illuminate\Support\Facades\Route;
//use Tymon\JWTAuth\Http\Middleware\Authenticate;
//use Tymon\JWTAuth\Middleware\RefreshToken;
//use Tymon\JWTAuth\Middleware\GetUserFromToken;



Route::post('/api/login',  [AuthController::class, 'login'] )
//->middleware( [GetUserFromToken::class, RefreshToken::class]);
//->middleware( [Authenticate::class, RefreshToken::class] )
;

Route::post('/api/contact/{lang}', [ContactController::class, 'create']); 


Route::group(['middleware' => ['jwt.auth']], function() {
    $apiSecret = '';
    Route::get( '/api/'.$apiSecret.'contacts', [ContactController::class, 'index']);  
    Route::delete( '/api/'.$apiSecret.'contacts/{id}', [ContactController::class, 'delete']);  

    Route::get('/api/'.$apiSecret.'config', [ConfigController::class, 'index']);
    Route::get('/api/'.$apiSecret.'config/clearcache', [ConfigController::class, 'clearCache']);
    Route::get('/api/'.$apiSecret.'config/createsitemap', [ConfigController::class, 'createSiteMap']);                

    Route::get('/api/'.$apiSecret.'pages', [PageController::class, 'index']);
    Route::get('/api/'.$apiSecret.'pages/{id}', [PageController::class,  'oneItemAdmin']);        
    Route::post('/api/'.$apiSecret.'pages', [PageController::class, 'create']);
    Route::put('/api/'.$apiSecret.'pages/{id}', [PageController::class,  'update']);
    Route::delete('/api/'.$apiSecret.'pages/{id}', [PageController::class,  'delete']);
    Route::get('/api/'.$apiSecret.'pages/position/{direction}/{id}', [PageController::class, 'position']);
    Route::get('/api/'.$apiSecret.'pages/type/{type}', [PageController::class,  'getPagesByType']);


    Route::get('/api/'.$apiSecret.'menus', [MenuController::class,  'index']);
    Route::post('/api/'.$apiSecret.'menus', [MenuController::class,    'create']);
    Route::put('/api/'.$apiSecret.'menus/{id}', [MenuController::class,   'update']);
    Route::delete('/api/'.$apiSecret.'menus/{id}', [MenuController::class,   'delete']);
    Route::get('/api/'.$apiSecret.'menus/position/{direction}/{id}', [MenuController::class,  'position']);

    Route::get('/api/'.$apiSecret.'logout', [AuthController::class,  'logout']);


});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
$demoStatus = false;  //env('DEMO_STATUS', false);

Route::get('productsGetNameAndPrice/{lang?}', 'ProductController@getNameAndPrice');

if($demoStatus){
    Route::post('login', 'AuthController@login');
    Route::get('comments/{pageId}', 'CommentController@index');
    Route::get('page/{id}/{lang}', 'PageController@oneItem');
    
    Route::group(['middleware' => ['jwt.auth']], function() {
        $apiSecret = env('API_SECRET', '' );
        if($apiSecret){
            $apiSecret = $apiSecret.'/';
        }            
        Route::get($apiSecret.'logout', 'AuthController@logout');
        Route::get($apiSecret.'menus', 'MenuController@index');

        Route::get($apiSecret.'pages', 'PageController@index');
        Route::get($apiSecret.'pages/type/{type}', 'PageController@getPagesByType');

        Route::get($apiSecret.'images/{type}/{pageId}', 'ImageController@getItemByTypeAndRefId'); //getItemByPageId
        //Route::get('images/position/{direction}/{id}', 'ImageController@position'); //only for type page is working

        Route::get($apiSecret.'users/clients', 'UserController@getClients');
        Route::get($apiSecret.'config', 'ConfigController@index');

        Route::get($apiSecret.'products', 'ProductController@index');

        Route::get($apiSecret.'contacts', 'ContactController@index');            
        Route::get($apiSecret.'checkouts', 'CheckoutController@index');                
    });
}else{
    #Route::post('register', 'AuthController@register');


    Route::post('login', 'AuthController@login');
    //Route::post('recover', 'AuthController@recover');

    Route::get('comments/{pageId}', 'CommentController@index');
    Route::post('comments/{pageId}', 'CommentController@create');
    Route::post('contact/{lang}', 'ContactController@create');    

    Route::get('page/{id}/{lang}', 'PageController@oneItem');

    Route::group(['middleware' => ['jwt.auth']], function() {
        $apiSecret = env('API_SECRET', '' );
        if($apiSecret){
            $apiSecret = $apiSecret.'/';
        }
        
        Route::get($apiSecret.'logout', 'AuthController@logout');

        Route::get($apiSecret.'pages', 'PageController@index');
        Route::get($apiSecret.'pages/{id}', 'PageController@oneItemAdmin');        
        Route::post($apiSecret.'pages', 'PageController@create');
        Route::put($apiSecret.'pages/{id}', 'PageController@update');
        Route::delete($apiSecret.'pages/{id}', 'PageController@delete');
        Route::get($apiSecret.'pages/position/{direction}/{id}', 'PageController@position');
        Route::get($apiSecret.'pages/type/{type}', 'PageController@getPagesByType');

        Route::get($apiSecret.'images/{type}/{pageId}', 'ImageController@getItemByTypeAndRefId'); //getItemByPageId
        Route::delete($apiSecret.'images/{id}',  'ImageController@delete');
        Route::get($apiSecret.'images/position/{direction}/{id}', 'ImageController@position'); //only for type page is working

        Route::get($apiSecret.'users/clients', 'UserController@getClients');
        
        Route::get($apiSecret.'menus', 'MenuController@index');
        Route::post($apiSecret.'menus', 'MenuController@create');
        Route::put($apiSecret.'menus/{id}', 'MenuController@update');
        Route::delete($apiSecret.'menus/{id}', 'MenuController@delete');
        Route::get($apiSecret.'menus/position/{direction}/{id}', 'MenuController@position');

        Route::get($apiSecret.'products', 'ProductController@index');
        Route::post($apiSecret.'products', 'ProductController@create');
        Route::put($apiSecret.'products/{id}', 'ProductController@update');
        Route::delete($apiSecret.'products/{id}', 'ProductController@delete');

        Route::get($apiSecret.'contacts', 'ContactController@index');    
        Route::delete($apiSecret.'contacts/{id}', 'ContactController@delete');            

        Route::get($apiSecret.'config', 'ConfigController@index');
        Route::get($apiSecret.'config/clearcache', 'ConfigController@clearCache');
        Route::get($apiSecret.'config/createsitemap', 'ConfigController@createSiteMap');                

        Route::get($apiSecret.'checkouts', 'CheckoutController@index');        
        Route::put($apiSecret.'checkouts/{id}', 'CheckoutController@update');        
    });
    //dd('_________________vvvvvvvv___________');    
}
*/