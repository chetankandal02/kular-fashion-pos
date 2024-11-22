<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/', [FrontendController::class, 'index'])->name('/');
});