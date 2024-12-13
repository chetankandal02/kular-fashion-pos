<?php

use App\Http\Controllers\GiftVoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/gift-voucher', [GiftVoucherController::class, 'create']);
Route::post('/gift-voucher/validate', [GiftVoucherController::class, 'validate']);