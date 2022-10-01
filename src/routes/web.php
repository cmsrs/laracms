<?php

use Cmsrs\Laracms\Controllers\InspirationController;
use Illuminate\Support\Facades\Route;

Route::get('inspire', InspirationController::class);
Route::get('trs', [InspirationController::class, 'trs'  ]);
