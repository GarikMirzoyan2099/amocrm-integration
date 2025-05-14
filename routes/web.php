<?php

use Illuminate\Support\Facades\Route;
use App\Integrations\AmoCrm\Controllers\AmoAuthController;
use App\Integrations\AmoCrm\Controllers\AmoDealController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/amocrm/auth', [AmoAuthController::class, 'redirectToAmoCRM'])->name('amocrm.auth');
Route::get('/amocrm/auth-callback', [AmoAuthController::class, 'callback'])->name('amocrm.auth-callback');