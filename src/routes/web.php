<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Cmsrs\Laracms\Models\Config;
use Cmsrs\Laracms\Models\Page;
use Cmsrs\Laracms\Controllers\FrontController;
use Cmsrs\Laracms\Controllers\HomeController;
use Cmsrs\Laracms\Controllers\Auth\LoginController;
use Cmsrs\Laracms\Controllers\Auth\RegisterController;
use Cmsrs\Laracms\Controllers\Auth\ForgotPasswordController;


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

    //Route::get('/home', 'HomeController@index')->name('home');


    //Route::get('/home/basket', 'HomeController@basket')->name('basket');
    //Route::get('/home/orders', 'HomeController@orders')->name('orders');

    //depreciate - /home/api/tobank!
    //Route::post('/home/api/tobank', 'HomeController@tobank')->name('tobank');
    Route::group(['middleware' => ['web']], function () {        
        Route::post('/post/checkout', [FrontController::class, 'postCheckout']);
    });

    Route::get('/changelang/{lang}/{pageId}/{productSlug?}', [FrontController::class,  'changeLang'])->name('changelang');
//});



    Route::get('/',  [FrontController::class,  'index']);
    Route::post('/logout', [LoginController::class,   'logout'])->name('logout');    
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


        Route::get('/'.Page::PREFIX_CMS_ONE_PAGE_IN_MENU_URL.'/{menuSlug}', [FrontController::class, 'getPage']);
        Route::get('/'.Page::PREFIX_CMS_URL.'/{menuSlug}/{pageSlug}/{productSlug?}', [FrontController::class,  'getPage']);
        Route::get('/'.Page::PREFIX_IN_URL.'/{pageSlug}', [FrontController::class,  'getSeparatePage']);    
    }else{

        //die('___________ssss_______');
        //Route::post('/logout', [LoginController::class,   'logout'])->name('logout');        


        Route::get('/{lang}/search', [FrontController::class,  'search']);


        Route::group(['middleware' => ['web']], function () {        
            Route::get('/{lang}/shoppingsuccess', [FrontController::class,  'shoppingsuccess']);            
            Route::get('/{lang}/checkout', [FrontController::class,  'checkout']);
        });


        Route::get('/{lang}/home', [HomeController::class,  'index']);        
        Route::get('/{lang}', [FrontController::class,  'index']);
        Route::get('/{lang}/login', [LoginController::class,  'showLoginForm']); //->name('login');    
        Route::get('/{lang}/register', [RegisterController::class,  'showRegistrationForm']); // ->name('register');                    
        Route::get('/{lang}/forgot', [ForgotPasswordController::class, 'showLinkRequestForm']);

        Route::get('/{lang}/'.Page::PREFIX_CMS_ONE_PAGE_IN_MENU_URL.'/{menuSlug}', [FrontController::class, 'getPageLangs']);        
        Route::get('/{lang}/'.Page::PREFIX_CMS_URL.'/{menuSlug}/{pageSlug}/{productSlug?}', [FrontController::class, 'getPageLangs']);
        Route::get('/{lang}/'.Page::PREFIX_IN_URL.'/{pageSlug}', [FrontController::class,  'getSeparatePageLangs']);
    }




//});