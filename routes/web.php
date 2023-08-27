<?php

use App\Http\Controllers\Admin\HomeViewController;
use App\Http\Controllers\Admin\BlandController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\FlavorController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MixController;
use App\Http\Controllers\Admin\SituationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Line\LoginController as LineLoginController;
use App\Http\Controllers\Line\MessageController;
use App\Http\Middleware\RedirectIfNull;
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

Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('loginForm');
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout')->name('logout');
});

// Route::controller(RegisterController::class)->group(function () {
//     Route::get('register', 'showRegistrationForm')->name('registerForm');
//     Route::post('register', 'register')->name('register');
// });

// Route::as('password.')->prefix('password')->group(function () {
//     Route::controller(ForgotPasswordController::class)->group(function () {
//         Route::get('/reset', 'showLinkRequestForm')->name('request');
//         Route::post('/email', 'sendResetLinkEmail')->name('email');
//     });

//     Route::controller(ResetPasswordController::class)->group(function () {
//         Route::get('/reset/{token}', 'showResetForm')->name('reset');
//         Route::post('/update', 'reset')->name('update');
//     });
// });

Route::controller(LineLoginController::class)->as('line.')->prefix('line')->group(function () {
    Route::get('/checkin', 'checkin')->name('checkin');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeViewController::class, 'index'])->name('home.index');

    Route::prefix('shop')->as('shop.')->middleware('role:high')->group(function () {
        Route::resource('/', ShopController::class)->except('show')->parameters(['' => 'id']);
        Route::get('/download', [ShopController::class, 'download'])->name('download');
    });

    Route::resource('/member', MemberController::class)->except('show')->parameters(['member' => 'id']);

    Route::resource('/mix', MixController::class)->parameters(['mix' => 'id']);
    Route::post('/mix/flavor', [MixController::class, 'getFlavors'])->name('mix.getFlavors');

    Route::resource('/bland', BlandController::class)->except('show')->parameters(['bland' => 'id']);

    Route::resource('/flavor', FlavorController::class)->except('show')->parameters(['flavor' => 'id']);

    Route::prefix('bill')->as('bill.')->group(function () {
        Route::resource('/', BillController::class)->parameters(['' => 'id']);
        Route::post('/draft', [BillController::class, 'draft'])->name('draft');
        Route::post('/get_members', [BillController::class, 'getMembers'])->name('getMembers');
        Route::post('/get_customers', [BillController::class, 'getCustomers'])->name('getCustomers');
    });

    Route::resource('/customer', CustomerController::class)->only(['index', 'show'])->parameters(['customer' => 'id']);

    Route::resource('/user', UserController::class)->middleware('role:high')->except('show')->parameters(['user' => 'id']);
    Route::resource('/situation', SituationController::class)->middleware('role:mid')->parameters(['situation' => 'id']);
});
