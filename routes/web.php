<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CrewCategoryController;
use App\Http\Controllers\WordPressAuthController;

// WordPress Authentication Routes
Route::get('/wp-login', [WordPressAuthController::class, 'showLoginForm'])
     ->name('wp.login.form');
Route::get('/login', function () {
    return redirect()->route('wp.login.form');
})->name('login');
Route::post('/wp-login', [WordPressAuthController::class, 'login'])
     ->name('wp.login');
Route::post('/wp-logout', [WordPressAuthController::class, 'logout'])
     ->name('wp.logout');

// Protected routes using WordPress auth
Route::middleware('auth:wordpress')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/post', [PostController::class, 'index'])->name('post');
    Route::get('/crew-category', [CrewCategoryController::class, 'index'])->name('crew.category');
    Route::get('/project', [ProjectController::class, 'index'])->name('project');
});

// Default redirect
Route::get('/', function () {
    if (Auth::guard('wordpress')->check()) {
        return redirect('/home');
    }
    return redirect('/wp-login');
});

Route::get('/debug-auth', function() {
    $guard = Auth::guard('wordpress');

    return [
        'is_authenticated' => $guard->check(),
        'user_id' => $guard->id(),
        'user' => $guard->user(),
        'session_data' => session()->all(),
        'session_id' => session()->getId(),
    ];
})->name('debug.auth');
