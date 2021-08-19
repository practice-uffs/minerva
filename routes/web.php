<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

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

Route::post('/telegram/webhook', [TelegramController::class, 'index']);
Route::get('/telegram/webhook/set', [TelegramController::class, 'setWebhook']);

Route::get('/', function () {
    return view('welcome');
});
