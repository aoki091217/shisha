<?php

use App\Http\Controllers\Admin\HomeViewController;
use App\Http\Controllers\Admin\BlandController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\FlavorController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OrderController;
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

Route::resource('/shop', ShopController::class)->except('show')->parameters(['shop' => 'id']);

Route::resource('/member', MemberController::class)->except('show')->parameters(['member' => 'id']);

Route::resource('/bland', BlandController::class)->except('show')->parameters(['bland' => 'id']);

Route::resource('/flavor', FlavorController::class)->except('show')->parameters(['flavor' => 'id']);

Route::resource('/order', OrderController::class)->parameters(['order' => 'id']);

Route::resource('/bill', BillController::class)->parameters(['bill' => 'id']);
Route::post('/bill/get_members', [BillController::class, 'getMembers'])->name('bill.getMembers');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
