<?php

use App\Http\Controllers\LinkedInAuthController;
use App\Http\Controllers\LinkedInPostController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    Route::get('/users', function () {
        return User::all();
    })->name('users');
    
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    
    Route::get('/auth/linkedin', [LinkedInAuthController::class, 'redirect'])->name('linkedin.redirect');
    Route::get('/auth/linkedin/callback', [LinkedInAuthController::class, 'callback'])->name('linkedin.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/post/confirm', [LinkedInPostController::class, 'confirm'])->name('post.confirm');
    Route::post('/post/publish', [LinkedInPostController::class, 'publish'])->name('post.publish');
    Route::get('/post/success', [LinkedInPostController::class, 'success'])->name('post.success');
    Route::get('/post/error', [LinkedInPostController::class, 'error'])->name('post.error');
    Route::post('/logout', [LinkedInAuthController::class, 'logout'])->name('logout');
});
