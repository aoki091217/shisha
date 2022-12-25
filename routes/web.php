<?php

use App\Http\Controllers\Admin\HomeViewController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Line\MessageController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('line.signed')->group(function () {
    Route::post('/line/webhook', [MessageController::class, 'webhook'])->name('line.webhook');
});

Route::get('/home', [HomeViewController::class, 'index'])->name('home.index');

Route::resource('/shop', ShopController::class)->except(['create', 'show'])->parameters(['shop' => 'id']);
