<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register');

});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/scanner', [ScannerController::class, 'index'])
        ->name('scanner');

    Route::get('/scanner/history', [ScannerController::class, 'history'])
        ->name('scanner.history');

    Route::get('/education', [EducationController::class, 'index'])
        ->name('education');

    Route::get('/education/{slug}', [EducationController::class, 'show'])
        ->name('education.show');

    Route::get('/chatbot', [ChatbotController::class, 'index'])
        ->name('chatbot');

    /*
    |--------------------------------------------------------------------------
    | Analytics
    |--------------------------------------------------------------------------
    */

    Route::view('/analytics', 'dashboard.analytics')
        ->name('analytics');

    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');

});