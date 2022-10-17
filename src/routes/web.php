<?php

use Cmsrs\Laracms\Controllers\InspirationController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Cmsrs\Laracms\Models\Config;
use Cmsrs\Laracms\Models\Page;
use Cmsrs\Laracms\Controllers\FrontController;
use Cmsrs\Laracms\Controllers\HomeController;
use Cmsrs\Laracms\Controllers\Auth\LoginController;
use Cmsrs\Laracms\Controllers\Auth\RegisterController;
use Cmsrs\Laracms\Controllers\Auth\ForgotPasswordController;


Route::get('inspire', InspirationController::class);
Route::get('trs', [InspirationController::class, 'trs'  ]);







/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/








/*
$demoStatus = env('DEMO_STATUS', false);
if($demoStatus){
    Auth::routes(['register' => false, 'reset' => false]);
}else{
    Auth::routes(['register' => true, 'reset' => true]);
}
*/




$langs = Config::arrGetLangsEnv();
//dd( count($langs));
//$langs = (new Config)->arrGetLangs();

//Route::group(['middleware' => ['web']], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //Route::get('/home/basket', 'HomeController@basket')->name('basket');
    //Route::get('/home/orders', 'HomeController@orders')->name('orders');

    //depreciate - /home/api/tobank!
    //Route::post('/home/api/tobank', 'HomeController@tobank')->name('tobank');
    Route::post('/post/checkout', 'FrontController@postCheckout');

    Route::get('/changelang/{lang}/{pageId}/{productSlug?}', 'FrontController@changeLang')->name('changelang');
//});



    Route::get('/',  [FrontController::class,  'index']);
    if( empty($langs)  || (1 == count($langs)) ){    
        
        


        Route::group(['middleware' => ['web']], function () {        
            Route::get('/shoppingsuccess',  [FrontController::class, 'shoppingsuccess']);        
            Route::get('/checkout',  [FrontController::class,  'checkout'] )->name('checkout');
            Route::get('/search',  [FrontController::class, 'search']);                
            Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');    
        });


        Route::get('/home', [HomeController::class, 'index' ])->name('home');
        
        Route::get('/register', [RegisterController::class,  'showRegistrationForm'])->name('register');            
        Route::get('/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('forgot');

        Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
        Route::get('/'.Page::PREFIX_CMS_ONE_PAGE_IN_MENU_URL.'/{menuSlug}', [FrontController::class, 'getPage']);
        Route::get('/'.Page::PREFIX_CMS_URL.'/{menuSlug}/{pageSlug}/{productSlug?}', [FrontController::class,  'getPage']);
        Route::get('/'.Page::PREFIX_IN_URL.'/{pageSlug}', [FrontController::class,  'getSeparatePage']);    
    }else{
        Route::get('/{lang}/shoppingsuccess', 'FrontController@shoppingsuccess');
        Route::get('/{lang}/search', 'FrontController@search');
        Route::get('/{lang}/checkout', 'FrontController@checkout');
        Route::get('/{lang}/home', 'HomeController@index');        
        Route::get('/{lang}', 'FrontController@index');
        Route::get('/{lang}/login', 'Auth\LoginController@showLoginForm'); //->name('login');    
        Route::get('/{lang}/register', 'Auth\RegisterController@showRegistrationForm'); // ->name('register');                    
        Route::get('/{lang}/forgot', 'Auth\ForgotPasswordController@showLinkRequestForm');

        Route::get('/{lang}/'.Page::PREFIX_CMS_ONE_PAGE_IN_MENU_URL.'/{menuSlug}', 'FrontController@getPageLangs');        
        Route::get('/{lang}/'.Page::PREFIX_CMS_URL.'/{menuSlug}/{pageSlug}/{productSlug?}', 'FrontController@getPageLangs');
        Route::get('/{lang}/'.Page::PREFIX_IN_URL.'/{pageSlug}', 'FrontController@getSeparatePageLangs');
    }




//});