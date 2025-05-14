<?php

use Illuminate\Support\Facades\Route;
use App\Integrations\AmoCrm\Controllers\AmoDealController;
use App\Integrations\AmoCrm\Controllers\AmoContactController;

Route::post('/amocrm/deal-created', [AmoDealController::class, 'handleDealCreated']);
Route::post('/amocrm/deal-updated', [AmoDealController::class, 'handleDealUpdated']);

Route::post('/amocrm/contact-created', [AmoContactController::class, 'handleContactCreated']);
Route::post('/amocrm/contact-updated', [AmoContactController::class, 'handleContactUpdated']);