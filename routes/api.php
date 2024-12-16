<?php

use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\GiftVoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/gift-voucher', [GiftVoucherController::class, 'create']);
Route::post('/gift-voucher/apply', [GiftVoucherController::class, 'apply']);

Route::post('/credit-note', [CreditNoteController::class, 'create']);
Route::post('/credit-note/apply', [CreditNoteController::class, 'apply']);