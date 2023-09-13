<?php

use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/currency/rate', [CurrencyController::class, 'getRate'])->name('get-rate');
Route::get('/currency/collect', [CurrencyController::class, 'collectRates'])->name('collect-rates');
